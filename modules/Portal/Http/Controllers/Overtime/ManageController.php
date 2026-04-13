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

    private function sendManageOvertimeNotification($targetUser, $data, $overtime)
    {
        try {
            if (!$targetUser) return;

            $targetUser->sendSystemNotification([
                'user_id_target' => $targetUser->id,
                'title'          => $data['title'],
                'message'        => $data['message'],
                'action'         => $data['action'] ?? 'Lihat Detail',
                'link'           => $data['link'],
                'icon'           => $data['icon'],
                'color'          => $data['color'],
                'sender_name'    => auth()->user()->name,
                'sender_image'   => auth()->user()->image_url ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error("Realtime Manage Overtime Notification Error: " . $e->getMessage());
        }
    }

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
                $this->sendManageOvertimeNotification($nextUser, [
                    'title'   => 'Persetujuan Lembur (Meneruskan)',
                    'message' => "Ada pengajuan lembur dari <strong>{$submitter->name}</strong> menunggu keputusan Anda.",
                    'link'    => route('portal::overtime.manage.show', $overtime->id),
                    'icon'    => 'bx bx-redo',
                    'color'   => 'info'
                ], $overtime);
            } else {
                $overtime->update(['paidable_at' => now()]);

                $this->sendManageOvertimeNotification($submitter, [
                    'title'   => 'Lembur Disetujui',
                    'message' => "Kabar baik! Pengajuan lembur Anda telah <strong>disetujui sepenuhnya</strong>.",
                    'link'    => route('portal::overtime.submission.show', $overtime->id),
                    'icon'    => 'bx bx-check-double',
                    'color'   => 'success'
                ], $overtime);
            }
        }

        if ($isRejected) {
            $overtime->update(['paidable_at' => null]);

            $this->sendManageOvertimeNotification($submitter, [
                'title'   => 'Lembur Ditolak',
                'message' => "Pengajuan lembur Anda telah ditolak. Silakan cek detail alasan dari atasan.",
                'link'    => route('portal::overtime.submission.show', $overtime->id),
                'icon'    => 'bx bx-x-circle',
                'color'   => 'danger'
            ], $overtime);
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
