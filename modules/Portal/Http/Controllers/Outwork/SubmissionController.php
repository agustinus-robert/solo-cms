<?php

namespace Modules\Portal\Http\Controllers\Outwork;

use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyOutworkCategory;
use Modules\HRMS\Models\EmployeeOutwork;
use Modules\HRMS\Models\EmployeePosition;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Outwork\Submission\StoreRequest;
use App\Notifications\GlobalGenericNotification;

class SubmissionController extends Controller
{

    private function sendOutworkNotification($targetUser, $type, $outwork)
    {
        try {
            if (!$targetUser || !$targetUser->id) return;

            $user = auth()->user();
            $categoryName = $outwork->category->name ?? 'Kegiatan Luar';

            $data = match($type) {
                'store' => [
                    'title'   => 'Persetujuan Insentif Baru',
                    'message' => "Karyawan <strong>{$user->name}</strong> mengajukan insentif untuk kegiatan <strong>{$categoryName}</strong>.",
                    'action'  => 'Tinjau Insentif',
                    'icon'    => 'bx bx-badge-check',
                    'color'   => 'warning',
                    'link'    => route('portal::outwork.manage.show', $outwork->id),
                ],
                'cancel' => [
                    'title'   => 'Pengajuan Insentif Dibatalkan',
                    'message' => "Pengajuan insentif <strong>{$categoryName}</strong> oleh <strong>{$user->name}</strong> telah dibatalkan.",
                    'action'  => 'Izin Dibatalkan',
                    'icon'    => 'bx bx-trash',
                    'color'   => 'secondary',
                    'link'    => '#',
                ],
                default => null,
            };

            if ($data) {
                $targetUser->sendSystemNotification([
                    'user_id_target' => $targetUser->id,
                    'title'          => $data['title'],
                    'message'        => $data['message'],
                    'action'         => $data['action'],
                    'link'           => $data['link'],
                    'icon'           => $data['icon'],
                    'color'          => $data['color'],
                    'sender_name'    => $user->name,
                    'sender_image'   => $user->image_url ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Realtime Outwork Notification Error: " . $e->getMessage());
        }
    }

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
        $category = CompanyOutworkCategory::find($request->input('category_id'));
        $categoryName = $category->name ?? 'Kegiatan Luar';

        $outwork = $employee->outworks()->create($request->transform());

        if ($request->has('approvables')) {
            foreach ($request->input('approvables') as $index => $positionId) {
                if ($positionId) {
                    $outwork->approvables()->create([
                        'userable_type' => EmployeePosition::class,
                        'userable_id'   => $positionId,
                        'result'        => ApprovableResultEnum::PENDING,
                        'level'         => $index + 1,
                    ]);
                }
            }
        }

        $firstApprover = $outwork->approvables()->orderBy('level')->first();

        if ($firstApprover && $firstApprover->userable) {
            $approverUser = $firstApprover->userable->employee->user;
            $this->sendOutworkNotification($approverUser, 'store', $outwork);
        } else {
            $outwork->update(['paidable_at' => now()]);
        }

        return redirect()->route('portal::outwork.submission.index', ['view' => 'mine'])
            ->with('success', 'Pengajuan berhasil dikirim!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeOutwork $outwork)
    {
        $employeeName = $outwork->employee->user->name;
        $categoryName = $outwork->category->name ?? 'Kegiatan Luar';

        $notifiedApprover = $outwork->approvables()
            ->orderBy('level')
            ->first();

        if ($notifiedApprover && $notifiedApprover->userable) {
            $targetUser = $notifiedApprover->userable->employee->user;
            $this->sendOutworkNotification($notifiedApprover->userable->employee->user, 'cancel', $outwork);
        }

        $outwork->delete();

        return redirect()->route('portal::outwork.submission.index', ['view' => 'mine'])
            ->with('success', 'Pengajuan telah dibatalkan.');
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
}
