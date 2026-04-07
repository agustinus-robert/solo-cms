<?php

namespace Modules\Portal\Http\Controllers\Outwork;

use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyOutworkCategory;
use Modules\HRMS\Models\EmployeeOutwork;
use Modules\HRMS\Models\EmployeePosition;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Outwork\Submission\StoreRequest;
use Modules\Portal\Notifications\Outwork\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Outwork\Cancelation\CanceledNotification;
use Modules\Portal\Notifications\Outwork\Submission\SubmissionWaNotification;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        $view = $request->get('view', 'mine');
        $myPositionIds = $employee->positions()->pluck('id')->toArray();

        $query = EmployeeOutwork::query()
            ->withTrashed()
            ->with(['approvables.userable.position', 'category', 'employee.user'])
            ->search($request->get('search'))
            ->whenPeriod($request->get('start_at'), $request->get('end_at'));

        if ($view == 'approvals') {
            $query->whereHas('approvables', function ($q) use ($myPositionIds) {
                $q->whereIn('userable_id', $myPositionIds);
            });
        } else {
            $query->where('empl_id', $employee->id);
        }

        $outworks = $query->latest()->paginate($request->get('limit', 10));

        $isApprover = EmployeeOutwork::whereHas('approvables', function ($q) use ($myPositionIds) {
             $q->whereIn('userable_id', $myPositionIds);
        })->exists();

        return view('portal::outwork.submission.index', compact('employee', 'outworks', 'view', 'isApprover'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(Request $request)
    {
        $employee = $request->user()->employee;

        $superiors = $employee->position->position->parents()
            ->with(['employeePositions' => fn($q) => $q->active()->with('employee.user')])
            ->get()
            ->map(function($parent) {
                return [
                    'level_value' => $parent->level,
                    'positions' => $parent->employeePositions
                ];
            })->filter(fn($item) => $item['positions']->count() > 0);

        $categories = CompanyOutworkCategory::all()->groupBy('name');

        return view('portal::outwork.submission.create', compact('employee', 'superiors', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $employee = $request->user()->employee;

        // 1. Create data outwork (menggunakan data yang sudah di-transform tanpa approvables)
        $outwork = $employee->outworks()->create($request->transform());

        // 2. Simpan Approver ke tabel relasi
        if ($request->has('approvables')) {
            foreach ($request->input('approvables') as $index => $positionId) {
                if ($positionId) {
                    $outwork->approvables()->create([
                        'userable_type' => EmployeePosition::class,
                        'userable_id'   => $positionId,
                        'result'        => ApprovableResultEnum::PENDING,
                        'level'         => $index + 1, // Kita buat level dinamis berdasarkan urutan input (1, 2, dst)
                    ]);
                }
            }
        }

        // 3. Ambil approver pertama (Level 1) untuk dikirim notifikasi
        $firstApprover = $outwork->approvables()->orderBy('level')->first();

        if ($firstApprover) {
            $approverUser = $firstApprover->userable->employee->user;

            // $approverUser->notify(new SubmissionNotification($outwork, null));
            // $approverUser->notify(new SubmissionWaNotification(
            //     "Halo, Anda mendapatkan pengajuan insentif dari {$employee->user->name}. Silakan cek di sini: " . route('portal::outwork.submission.show', $outwork->id),
            //     $approverUser
            // ));
        } else {
            $outwork->update(['paidable_at' => now()]);
        }

        return redirect()->route('portal::outwork.submission.index', ['view' => 'mine'])
            ->with('success', 'Pengajuan berhasil dikirim!');
    }
    /**
     * Display the specified resource.
     */
    public function show(EmployeeOutwork $outwork, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        $outwork->load('approvables.userable.position', 'category', 'employee.user');

        return view('portal::outwork.submission.show', compact('user', 'employee', 'outwork'));
    }

    public function destroy(EmployeeOutwork $outwork)
    {
        // Notifikasi pembatalan ke atasan yang sudah merespons (jika ada)
        $notifiedApprover = $outwork->approvables()
            ->where('result', '!=', ApprovableResultEnum::PENDING)
            ->first();

        if ($notifiedApprover) {
            $notifiedApprover->userable->employee->user->notify(new CanceledNotification($outwork));
        }

        $outwork->delete();

        return redirect()->route('portal::outwork.submission.index', ['view' => 'mine'])
            ->with('success', 'Pengajuan telah dibatalkan.');
    }
}
