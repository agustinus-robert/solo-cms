<?php

namespace Modules\Portal\Http\Controllers\Leave;

use Illuminate\Http\Request;
use Modules\HRMS\Models\EmployeeLeave;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Leave\Manage\UpdateRequest;
use App\Notifications\GlobalGenericNotification;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee->load('position.position.children');

        $leaves = EmployeeLeave::with('approvables.userable.position', 'employee.user')
            ->whereHas('employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending($request->get('pending'))
            ->search($request->get('search'))
            ->latest()
            ->paginate($request->get('limit', 10));

        $pending_leaves_count = EmployeeLeave::whereHas('employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending(true)
            ->count();

        return view('portal::leave.manage.index', compact('user', 'employee', 'leaves', 'pending_leaves_count'));
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeLeave $leave, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $results = config('modules.core.features.services.leaves.approvable_enum_available');

        $leave = $leave->load('approvables.userable.position');

        return view('portal::leave.manage.show', compact('user', 'employee', 'leave', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $approvable->update($request->transformed()->toArray());
        $submitter = $approvable->modelable->employee->user;

        if ($request->input('result') == ApprovableResultEnum::APPROVE->value) {

            $submitter->notify(new GlobalGenericNotification([
                'title'   => 'Pengajuan Disetujui',
                'message' => "Pengajuan Anda telah disetujui pada level {$approvable->level}. Menunggu proses selanjutnya.",
                'link'    => route('hrms::service.leave.manage.index'),
                'icon'    => 'bx bx-check-circle',
                'color'   => 'success'
            ]));

            $superiorApprovable = $approvable->modelable->approvables
                ->sortBy('level')
                ->filter(fn($a) => $a->level > $approvable->level)
                ->first();

            if ($superiorApprovable && $superiorApprovable->userable) {
                $nextApprover = $superiorApprovable->userable->employee->user;

                $nextApprover->notify(new GlobalGenericNotification([
                    'title'   => 'Perlu Persetujuan',
                    'message' => "Ada pengajuan baru dari {$submitter->name} yang memerlukan persetujuan Anda.",
                    'link'    => url()->current(),
                    'icon'    => 'bx bx-user-voice',
                    'color'   => 'info'
                ]));
            }
        }

        if ($request->input('result') == ApprovableResultEnum::REJECT->value) {
            $submitter->notify(new GlobalGenericNotification([
                'title'   => 'Pengajuan Ditolak',
                'message' => "Mohon maaf, pengajuan Anda ditolak pada level {$approvable->level}.",
                'link'    => route('hrms::service.leave.manage.index'),
                'icon'    => 'bx bx-x-circle',
                'color'   => 'danger'
            ]));
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }
}
