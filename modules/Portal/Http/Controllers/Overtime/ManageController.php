<?php

namespace Modules\Portal\Http\Controllers\Overtime;

use Illuminate\Http\Request;
use Modules\Account\Notifications\AccountNotification;
use Modules\HRMS\Models\EmployeeOvertime;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Overtime\Manage\UpdateRequest;
use App\Notifications\GlobalGenericNotification;
use Modules\Portal\Notifications\Overtime\Manage\ApprovedNotification;
use Modules\Portal\Notifications\Overtime\Manage\RejectedNotification;
use Modules\Portal\Notifications\Overtime\Submission\SubmissionNotification;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee->load('position.position.children');
        $start_at = $request->get('start_at', date('Y-m-01', strtotime("-1 months"))) . ' 00:00:00';
        $end_at = $request->get('end_at', date('Y-m-t')) . ' 23:59:59';

        $overtimes = EmployeeOvertime::with('approvables.userable.position', 'employee.user')
            ->whereHas('approvables', fn($approvable) => $approvable->where('userable_id', $employee->position->id))
            ->orWhere('scheduled_by', $employee->id)
            ->whenOnlyPending($request->get('pending'))
            ->whenPeriod($start_at, $end_at)
            ->search($request->get('search'))
            ->latest()
            ->paginate($request->get('limit', 10));

        $pending_overtimes_count = EmployeeOvertime::whereHas('employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending(true)
            ->count();

        return view('portal::overtime.manage.index', compact('user', 'employee', 'overtimes', 'pending_overtimes_count', 'start_at', 'end_at'));
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeOvertime $overtime, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $results = ApprovableResultEnum::cases();

        $overtime = $overtime->load('approvables.userable.position');

        return view('portal::overtime.manage.show', compact('user', 'employee', 'overtime', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $approvable->update($request->transformed()->toArray());

        $overtime = $approvable->modelable;
        if (!$overtime) {
            return redirect()->back()->with('error', 'Data lembur tidak ditemukan.');
        }

        $submitter = $overtime->employee->user;
        $result = (int) $request->input('result');
        $isApproved = $result === ApprovableResultEnum::APPROVE->value;
        $isRejected = $result === ApprovableResultEnum::REJECT->value;

        if ($isApproved) {
            $nextSuperior = $overtime->approvables
                ->sortBy('level')
                ->filter(fn($a) => $a->level > $approvable->level && $a->result === ApprovableResultEnum::PENDING)
                ->first();

            if ($nextSuperior && $nextSuperior->userable) {
                $nextUser = $nextSuperior->userable->employee->user;
                $nextUser->notify(new GlobalGenericNotification([
                    'title'   => 'Persetujuan Lembur (Meneruskan)',
                    'message' => "Ada pengajuan lembur dari <strong>{$submitter->name}</strong> yang telah disetujui di level sebelumnya dan menunggu keputusan Anda.",
                    'link'    => route('portal::overtime.manage.show', $overtime->id),
                    'icon'    => 'bx bx-redo',
                    'color'   => 'info'
                ]));
            } else {
                $overtime->update(['paidable_at' => now()]);

                $submitter->notify(new GlobalGenericNotification([
                    'title'   => 'Lembur Disetujui',
                    'message' => "Kabar baik! Pengajuan lembur Anda telah <strong>disetujui sepenuhnya</strong> dan siap diproses ke sistem penggajian.",
                    'link'    => route('portal::overtime.submission.show', $overtime->id),
                    'icon'    => 'bx bx-check-double',
                    'color'   => 'success'
                ]));
            }
        }

        if ($isRejected) {
            $overtime->update(['paidable_at' => null]);
            $submitter->notify(new GlobalGenericNotification([
                'title'   => 'Lembur Ditolak',
                'message' => "Pengajuan lembur Anda telah ditolak. Silakan cek detail pengajuan untuk melihat alasan atau catatan dari atasan.",
                'link'    => route('portal::overtime.submission.show', $overtime->id),
                'icon'    => 'bx bx-x-circle',
                'color'   => 'danger'
            ]));
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeOvertime $overtime)
    {
        $this->authorize('destroy', $overtime);

        $employeeName = $overtime->employee->user->name;
        $overtime->delete();

        return redirect()->back()->with('success', "Pengajuan lembur <strong>{$employeeName}</strong> berhasil dihapus");
    }
}
