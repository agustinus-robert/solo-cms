<?php

namespace Modules\Portal\Http\Controllers\Vacation;

use Illuminate\Http\Request;
use Modules\Account\Notifications\AccountNotification;
use Modules\HRMS\Models\EmployeeVacation;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Vacation\Manage\UpdateRequest;
use Modules\Portal\Notifications\Vacation\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Vacation\Manage\ApprovedNotification;
use Modules\Portal\Notifications\Vacation\Manage\RejectedNotification;
use Modules\Portal\Notifications\Vacation\Manage\AskForRevisionNotification;
use Modules\Portal\Notifications\Vacation\Cancelation\ApprovedNotification as CancelationApprovedNotification;
use Modules\Portal\Notifications\Vacation\Cancelation\RejectedNotification as CancelationRejectedNotification;
use App\Notifications\GlobalGenericNotification;

class ManageController extends Controller
{
    private function sendManageVacationNotification($targetUser, $data)
    {
        try {
            if (!$targetUser) return;

            $targetUser->sendSystemNotification([
                'user_id_target' => $targetUser->id, // Wajib ada untuk Model User
                'title'          => $data['title'],
                'message'        => $data['message'],
                'action'         => $data['action'] ?? 'Tinjau Detail',
                'link'           => $data['link'],
                'icon'           => $data['icon'],
                'color'          => $data['color'],
                'sender_name'    => auth()->user()->name,
                'sender_image'   => auth()->user()->image_url ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error("Realtime Manage Vacation Notification Error: " . $e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee->load('position.position.children');

        $vacations = EmployeeVacation::with('approvables.userable.position', 'quota.employee.user')
            ->whereHas('quota.employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending($request->get('pending'))
            ->search($request->get('search'))
            ->latest()
            ->paginate($request->get('limit', 10));

        $pending_vacations_count = EmployeeVacation::whereHas('quota.employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending(true)
            ->count();

        return view('portal::vacation.manage.index', compact('user', 'employee', 'vacations', 'pending_vacations_count'));
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeVacation $vacation, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $results = ApprovableResultEnum::cases();

        if (isset($vacation->dates->first()['cashable'])) {
            unset($results[array_search(ApprovableResultEnum::REVISION, $results)]);
        }

        $vacation = $vacation->load('approvables.userable.position');

        return view('portal::vacation.manage.show', compact('user', 'employee', 'vacation', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $data = $request->transform();
        $approvable->update($data->toArray());
        $model = $approvable->modelable;

        if (!$model) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $resultValue = (int) $request->input('result');
        $submitter = $model->quota->employee->user;

        if ($approvable->cancelable) {
            $model->approvables()->update($data->only('result')->toArray());

            if ($resultValue == ApprovableResultEnum::APPROVE->value) {
                $model->update(['dates' => $model->dates->filter(fn($d) => empty($d['c']))]);

                $this->sendManageVacationNotification($submitter, [
                    'title'   => 'Pembatalan Cuti Disetujui',
                    'message' => "Permintaan pembatalan cuti Anda untuk <strong>{$model->description}</strong> telah disetujui.",
                    'link'    => route('portal::vacation.submission.show', $model->id),
                    'icon'    => 'bx bx-undo',
                    'color'   => 'success'
                ]);
            }

            if ($resultValue == ApprovableResultEnum::REJECT->value) {
                $model->update([
                    'dates' => $model->dates->map(function ($date) {
                        $date['c'] = false;
                        return array_filter($date);
                    })
                ]);

                $this->sendManageVacationNotification($submitter, [
                    'title'   => 'Pembatalan Cuti Ditolak',
                    'message' => "Permintaan pembatalan cuti Anda ditolak. Status cuti tetap berjalan.",
                    'link'    => route('portal::vacation.submission.show', $model->id),
                    'icon'    => 'bx bx-error-alt',
                    'color'   => 'danger'
                ]);
            }
        } else {
            if ($resultValue == ApprovableResultEnum::APPROVE->value) {
                $this->handleApproval($approvable, $model, $submitter);
            }

            if ($resultValue == ApprovableResultEnum::REJECT->value) {
                $this->sendManageVacationNotification($submitter, [
                    'title'   => 'Pengajuan Cuti Ditolak',
                    'message' => "Mohon maaf, pengajuan cuti Anda untuk <strong>{$model->description}</strong> ditolak.",
                    'link'    => route('portal::vacation.submission.show', $model->id),
                    'icon'    => 'bx bx-x-circle',
                    'color'   => 'danger'
                ]);
            }

            if ($resultValue == ApprovableResultEnum::REVISION->value) {
                $this->sendManageVacationNotification($submitter, [
                    'title'   => 'Revisi Pengajuan Cuti',
                    'message' => "Pengajuan cuti Anda memerlukan revisi. Silakan cek catatan atasan.",
                    'link'    => route('portal::vacation.submission.show', $model->id),
                    'icon'    => 'bx bx-edit',
                    'color'   => 'warning'
                ]);
            }
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }


    protected function handleApproval($approvable, $model, $submitter)
    {
        $superior = $model->approvables
            ->sortBy('level')
            ->filter(fn($a) => $a->level > $approvable->level && $a->result === ApprovableResultEnum::PENDING)
            ->first();

        if ($superior && $superior->userable) {
            $nextUser = $superior->userable->employee->user;
            $this->sendManageVacationNotification($nextUser, [
                'title'   => 'Persetujuan Cuti Baru',
                'message' => "Ada pengajuan cuti dari <strong>{$submitter->name}</strong> menunggu persetujuan Anda.",
                'link'    => route('portal::vacation.manage.show', $model->id),
                'icon'    => 'bx bx-file-find',
                'color'   => 'info'
            ]);
        } else {
            $this->sendManageVacationNotification($submitter, [
                'title'   => 'Pengajuan Cuti Disetujui',
                'message' => "Selamat! Pengajuan cuti Anda untuk <strong>{$model->description}</strong> telah disetujui sepenuhnya.",
                'link'    => route('portal::vacation.submission.show', $model->id),
                'icon'    => 'bx bx-check-double',
                'color'   => 'success'
            ]);
        }
    }
    /**
     * Send approval notifications.
     */
    public function sendApprovalNotifications(CompanyApprovable $approvable)
    {
        $approvable->modelable->quota->employee->user->notify(new ApprovedNotification($approvable->modelable, $approvable, isset($approvable->modelable->dates->first()['cashable'])));
        $this->sendAccountNotification($approvable->modelable->quota->employee->user, $approvable, 'approved');
        if ($superior = $approvable->modelable->approvables->sortBy('level')->filter(fn($a) => $a->level > $approvable->level)->first()) {
            $superior->userable->employee->user->notify(new SubmissionNotification($approvable->modelable, false, $approvable->userable));
            $this->sendAccountNotification($superior->userable->employee->user, $approvable, 'submission', $approvable->userable->name);
        }
    }

    /**
     * Send rejection notifications.
     */
    protected function sendRejectionNotifications(CompanyApprovable $approvable, $cancelable = false)
    {
        $cancelable
            ? $approvable->modelable->quota->employee->user->notify(new CancelationRejectedNotification($approvable->modelable))
            : $approvable->modelable->quota->employee->user->notify(new RejectedNotification($approvable->modelable, $approvable));

        $this->sendAccountNotification($approvable->modelable->quota->employee->user, $approvable, 'rejection');
    }

    /**
     * Send rejection notifications.
     */
    protected function sendCancelableNotifications(CompanyApprovable $approvable)
    {
        $approvable->modelable->quota->employee->user->notify(new CancelationApprovedNotification($approvable->modelable));
        $this->sendAccountNotification($approvable->modelable->quota->employee->user, $approvable, 'cancelable');
    }

    /**
     * Send rejection notifications.
     */
    protected function sendRevisionNotifications(CompanyApprovable $approvable)
    {
        $approvable->modelable->quota->employee->user->notify(new AskForRevisionNotification($approvable->modelable, $approvable));
        $this->sendAccountNotification($approvable->modelable->quota->employee->user, $approvable, 'revision');
    }

    /**
     * Send a single AccountNotification based on the type.
     */
    protected function sendAccountNotification($user, CompanyApprovable $approvable, $type, $submitterName = null)
    {
        $message = match ($type) {
            'submission' => "{$submitterName} mengajukan cuti untuk keperluan {$approvable->modelable->description}, silakan cek pada link berikut " . route('portal::vacation.manage.show', ['vacation' => $approvable->modelable->id]),
            'cancelable' => "{$submitterName} mengajukan pembatalan cuti untuk keperluan {$approvable->modelable->description}, silakan cek pada link berikut " . route('portal::vacation.manage.show', ['vacation' => $approvable->modelable->id]),
            'approved' => "Selamat, pengajuan cuti untuk keperluan {$approvable->modelable->description} disetujui, silakan cek pada link berikut " . route('portal::vacation.submission.show', ['vacation' => $approvable->modelable->id]),
            'rejection' => "Maaf, pengajuan cuti untuk keperluan {$approvable->modelable->description} ditolak, silakan cek pada link berikut " . route('portal::vacation.submission.show', ['vacation' => $approvable->modelable->id]),
            'revision' => "Maaf, pengajuan cuti untuk keperluan {$approvable->modelable->description} memerlukan revisi, silakan cek pada link berikut " . route('portal::vacation.submission.show', ['vacation' => $approvable->modelable->id]),
            default => ''
        };
        $user->notify(new AccountNotification($message, $user));
    }
}
