<?php

namespace Modules\Portal\Http\Controllers\Outwork;

use Illuminate\Http\Request;
use Modules\HRMS\Models\EmployeeOutwork;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Outwork\Manage\UpdateRequest;
use App\Notifications\GlobalGenericNotification;

class ManageController extends Controller
{
    private function sendManageOutworkNotification($targetUser, $data)
    {
        try {
            if (!$targetUser) return;

            $targetUser->sendSystemNotification([
                'user_id_target' => $targetUser->id,
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
            \Log::error("Realtime Manage Outwork Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee->load('position.position.children');

        $outworks = EmployeeOutwork::with('approvables.userable.position', 'employee.user', 'category')
            ->whereHas('approvables', fn($approvable) => $approvable->where('userable_id', $employee->position->id))
            ->whenOnlyPending($request->get('pending'))
            ->search($request->get('search'))
            ->latest()
            ->paginate($request->get('limit', 10));

        $pending_outworks_count = EmployeeOutwork::whereHas('employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending(true)
            ->count();

        return view('portal::outwork.manage.index', compact('user', 'employee', 'outworks', 'pending_outworks_count'));
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeOutwork $outwork, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $results = ApprovableResultEnum::cases();

        $outwork = $outwork->load('approvables.userable.position', 'category');

        return view('portal::outwork.manage.show', compact('user', 'employee', 'outwork', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $approvable->update($request->transformed()->toArray());
        $outwork = $approvable->modelable;

        if (!$outwork) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $result = (int) $request->input('result');
        $isApproved = $result === ApprovableResultEnum::APPROVE->value;
        $isRejected = $result === ApprovableResultEnum::REJECT->value;

        $submitter = $outwork->employee->user;
        $categoryName = $outwork->category->name ?? 'Kegiatan Luar';

        $allApprovers = $outwork->approvables()->orderBy('level', 'asc')->get();
        $lastApprover = $allApprovers->last();

        if ($isApproved) {
            if ($approvable->id === $lastApprover->id) {
                $outwork->update(['paidable_at' => now()]);

                $this->sendManageOutworkNotification($submitter, [
                    'title'   => 'Insentif Disetujui Sepenuhnya',
                    'message' => "Selamat! Pengajuan insentif <strong>{$categoryName}</strong> Anda telah disetujui sepenuhnya.",
                    'link'    => route('portal::outwork.submission.show', $outwork->id),
                    'icon'    => 'bx bx-check-double',
                    'color'   => 'success'
                ]);
            } else {
                $nextSuperior = $allApprovers->where('level', '>', $approvable->level)
                                            ->where('result', ApprovableResultEnum::PENDING)
                                            ->first();

                if ($nextSuperior && $nextSuperior->userable) {
                    $nextUser = $nextSuperior->userable->employee->user;

                    $this->sendManageOutworkNotification($nextUser, [
                        'title'   => 'Perlu Approval Insentif',
                        'message' => "Ada pengajuan insentif <strong>{$categoryName}</strong> dari <strong>{$submitter->name}</strong> menunggu persetujuan Anda.",
                        'link'    => route('portal::outwork.manage.show', $outwork->id),
                        'icon'    => 'bx bx-time-five',
                        'color'   => 'info'
                    ]);
                }
            }
        }

        if ($isRejected) {
            $outwork->update(['paidable_at' => null]);

            $this->sendManageOutworkNotification($submitter, [
                'title'   => 'Insentif Ditolak',
                'message' => "Mohon maaf, pengajuan insentif <strong>{$categoryName}</strong> Anda ditolak. Silakan cek catatan atasan.",
                'link'    => route('portal::outwork.submission.show', $outwork->id),
                'icon'    => 'bx bx-x-circle',
                'color'   => 'danger'
            ]);
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }
}
