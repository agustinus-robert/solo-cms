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
        $result = (string) $request->input('result');

        if ($result == (string) ApprovableResultEnum::APPROVE->value) {

            $this->sendApprovalNotification($submitter, 'approve', $approvable);
            $superiorApprovable = $approvable->modelable->approvables
                ->sortBy('level')
                ->filter(fn($a) => $a->level > $approvable->level)
                ->first();

            if ($superiorApprovable && $superiorApprovable->userable) {
                $nextApprover = $superiorApprovable->userable->employee->user;

                $this->sendApprovalNotification($nextApprover, 'next_level', $approvable, $submitter);
            }
        }

        if ($result == ApprovableResultEnum::REJECT->value) {
            $this->sendApprovalNotification($submitter, 'reject', $approvable);
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }

    /**
     * Private function untuk standarisasi notifikasi approval
     */
    private function sendApprovalNotification($targetUser, $type, $approvable, $submitter = null)
    {
        try {
            if (!$targetUser || !isset($targetUser->id)) {
                \Log::warning("Leave Notification: Target user tidak valid.");
                return;
            }

            $user = auth()->user();

            $submitterName = ($submitter instanceof \Modules\Account\Models\User)
                ? $submitter->name
                : ($submitter->name ?? 'Karyawan');

            $data = match($type) {
                'approve' => [
                    'title'   => 'Pengajuan Disetujui',
                    'message' => "Pengajuan Anda telah disetujui oleh ".$user->name,
                    'action'  => 'Izin Disetujui',
                    'icon'    => 'bx bx-check-circle',
                    'color'   => 'success',
                    'link'    => route('portal::leave.submission.index'),
                ],
                'reject' => [
                    'title'   => 'Pengajuan Ditolak',
                    'message' => "Mohon maaf, pengajuan Anda ditolak pada level {$approvable->level}.",
                    'action'  => 'Izin Ditolak',
                    'icon'    => 'bx bx-x-circle',
                    'color'   => 'danger',
                    'link'    => route('portal::leave.submission.index'),
                ],
                'next_level' => [
                    'title'   => 'Perlu Persetujuan',
                    'message' => "Ada pengajuan baru dari {$submitterName} yang memerlukan persetujuan Anda.",
                    'action'  => 'Persetujuan Izin',
                    'icon'    => 'bx bx-user-voice',
                    'color'   => 'info',
                    'link'    => route('hrms::service.leave.manage.index'),
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
            // Log lebih detail supaya ketahuan salahnya di mana
            \Log::error("Realtime Leave Approval Notification Error: " . $e->getMessage(), [
                'type' => $type,
                'user_id' => $targetUser->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
