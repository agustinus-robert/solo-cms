<?php

namespace Modules\Portal\Http\Controllers\Vacation;

use Illuminate\Http\Request;
use Modules\Account\Notifications\AccountNotification;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\HRMS\Models\EmployeeVacation;
use Modules\Core\Models\CompanyApprovable;
use Modules\HRMS\Models\EmployeePosition;
use Illuminate\Support\Facades\DB;
use Modules\HRMS\Models\EmployeeVacationQuota;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Vacation\Submission\StoreRequest;
use Modules\Portal\Http\Requests\Vacation\Submission\UpdateRequest;
use Modules\Portal\Notifications\Vacation\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Vacation\Submission\RevisedNotification;
use Modules\Portal\Notifications\Vacation\Submission\CanceledNotification;

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

    $start_at = $request->get('start_at');
    $end_at   = $request->get('end_at');

    $query = EmployeeVacation::query()
        ->withTrashed()
        ->with(['quota.employee.user', 'quota.category', 'approvables.userable.position']);

    if ($view === 'approvals') {
        $myPositionIds = $employee->positions()->pluck('id')->toArray();

        $query->whereHas('approvables', function($q) use ($myPositionIds) {
            $q->where('userable_type', EmployeePosition::class)
              ->whereIn('userable_id', $myPositionIds);
        });
    } else {
        $query->whereHas('quota', function($q) use ($employee) {
            $q->where('empl_id', $employee->id);
        });
    }

    if ($request->filled('search')) {
        $query->search($request->get('search'));
    }

    if ($request->filled('start_at') && $request->filled('end_at')) {
        $query->where(function($q) use ($start_at, $end_at) {
            $q->whereDate('created_at', '>=', $start_at)
              ->whereDate('created_at', '<=', $end_at)
              ->orWhereRaw("exists (select 1 from jsonb_array_elements(dates::jsonb) as elem where (elem->>'d')::date >= ? and (elem->>'d')::date <= ?)", [$start_at, $end_at]);
        });
    }

    $vacations = $query->latest()->paginate($request->get('limit', 10));
    $isApprover = $employee->position?->position?->children()->exists() ?? false;

    $quotas = collect();
    if ($view === 'mine') {
        $quotas = $employee->vacationQuotas()
            ->with(['category', 'vacations.approvables'])
            ->active()
            ->get()
            ->sortBy('category.type.value')
            ->filter(fn($quota) => $quota->category?->type?->quotaVisibility());
    }

    return view('portal::vacation.submission.index', compact(
        'employee', 'vacations', 'quotas', 'view', 'isApprover', 'start_at', 'end_at'
    ));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = $request->user()->employee;

        $quotas = $employee->vacationQuotas()->with('category', 'vacations')->active()->get();

        if (!$quotas->count()) {
            return redirect()->back()->with('danger', 'Maaf, sepertinya belum ada kuota cuti tahun ini nih, silakan hubungi admin untuk keterangan lebih lanjut!');
        }

        return view('portal::vacation.submission.create', compact('employee', 'quotas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        return DB::transaction(function () use ($employee, $request) {
            $vacation = $employee->vacations()->create($request->transformed()->toArray());
            if ($employee->position && $employee->position->position) {
                $parents = $employee->position->position->parents->reverse()->values();

                foreach ($parents as $index => $parent) {
                    $approverPosition = $parent->employeePositions()->active()->first();

                    if ($approverPosition) {
                        $vacation->createApprovable($approverPosition, [
                            'level' => $index + 1
                        ]);
                    }
                }
            }

            $firstApprover = $vacation->approvables()->orderBy('level', 'asc')->first();

            if ($firstApprover && $firstApprover->userable) {
                $targetUser = $firstApprover->userable->getUser();

                if ($targetUser) {
                    // $targetUser->notify(new SubmissionNotification($vacation, false, null));

                    $message = $employee->user->name . ' mengajukan cuti ' .
                            ($vacation->quota->category->name ?? 'Tahunan') .
                            ', silakan cek pada link berikut: ' .
                            route('portal::vacation.manage.show', ['vacation' => $vacation->id]);

                    // $targetUser->notify(new AccountNotification($message, $targetUser));
                }
            }

            $hasApprover = $vacation->approvables()->exists();

            return redirect()->route('portal::vacation.submission.index')->with(
                'success',
                $hasApprover
                    ? 'Pengajuan cuti berhasil dikirim ke atasan langsung!'
                    : 'Pengajuan otomatis disetujui sistem.'
            );
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeVacation $vacation, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        $myPositionIds = $employee->positions()->pluck('id')->toArray();

        $vacation = $vacation->load(['approvables.userable.position', 'quota.employee.user', 'quota.category']);

        return view('portal::vacation.submission.show', compact('user', 'employee', 'vacation', 'myPositionIds'));
    }

    /**
     * Show the form for editing a specified resource.
     */
    public function edit(EmployeeVacation $vacation, Request $request)
    {
        $employee = $request->user()->employee;

        $quotas = $employee->vacationQuotas()->with('category', 'vacations')->active()->get();

        return view('portal::vacation.submission.edit', compact('employee', 'quotas', 'vacation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeVacation $vacation, UpdateRequest $request)
    {
        $employee = $request->user()->employee;

        $vacation->fill($request->transformed()->toArray())->save();

        // Reset approvable
        foreach ($vacation->approvables as $approvable) {
            $approvable->fill([
                'result' => ApprovableResultEnum::PENDING,
                'reason' => null,
                'history' => $approvable->result == ApprovableResultEnum::REVISION ? $approvable->only('result', 'reason', 'cancelable') : null
            ])->save();
        }

        // Handle notifications
        if ($approvable = $vacation->approvables()->orderBy('level')->first()) {
            $approvable->userable->employee->user->notify(new RevisedNotification($vacation));
        }

        return redirect()->route('portal::vacation.submission.index')->with('success', 'Pengajuan hasil revisi sudah dikirim ulang, silakan tunggu notifikasi selanjutnya dari atasan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeVacation $vacation)
    {
        $vacation->delete();

        // Handle notifications
        if ($approvable = $vacation->approvables()->whereNotIn('result', [ApprovableResultEnum::PENDING])->orderBy('level')->first()) {
            $approvable->userable->employee->user->notify(new CanceledNotification($vacation));
        }

        return redirect()->route('portal::vacation.submission.index')->with('success', 'Pengajuan telah dibatalkan dan kami telah mengirim notifikasi ke atasan!');
    }
}
