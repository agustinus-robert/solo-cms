<?php

namespace Modules\Portal\Http\Controllers\Outwork;

use Illuminate\Http\Request;
use Modules\HRMS\Models\EmployeeOutwork;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Outwork\Manage\UpdateRequest;
use Modules\Portal\Notifications\Outwork\Manage\ApprovedNotification;
use Modules\Portal\Notifications\Outwork\Manage\RejectedNotification;
use Modules\Portal\Notifications\Outwork\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Outwork\Manage\ManageWaNotification;

class ManageController extends Controller
{
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

        $allApprovers = $outwork->approvables()->orderBy('level', 'asc')->get();
        $lastApprover = $allApprovers->last();

        $nextSuperior = $allApprovers->where('level', '>', $approvable->level)
                                    ->where('result', ApprovableResultEnum::PENDING)
                                    ->first();

        // 3. Jika DISETUJUI
        if ($isApproved) {
            // Jika ini adalah pemberi persetujuan terakhir
            if ($approvable->id === $lastApprover->id) {
                $outwork->update(['paidable_at' => now()]);

                // Notifikasi Selesai ke Pembuat Pengajuan
                // $outwork->employee->user->notify(new ManageWaNotification(
                //     "Halo {$outwork->employee->user->name}, pengajuan insentif Anda telah DISETUJUI SEPENUHNYA. Cek detail: " . route('portal::outwork.submission.show', $outwork->id),
                //     $outwork->employee->user
                // ));
            } else {
                // Jika masih ada atasan selanjutnya, kirim WA ke atasan berikutnya
                if ($nextSuperior) {
                    $nextUser = $nextSuperior->userable->employee->user;
                    // $nextUser->notify(new ManageWaNotification(
                    //     "Halo, ada pengajuan insentif dari {$outwork->employee->user->name} yang menunggu persetujuan Anda. Link: " . route('portal::outwork.submission.show', $outwork->id),
                    //     $nextUser
                    // ));
                }
            }
        }

        // 4. Jika DITOLAK
        if ($isRejected) {
            $outwork->update(['paidable_at' => null]);

            // Notifikasi Penolakan ke Pembuat Pengajuan
            // $outwork->employee->user->notify(new ManageWaNotification(
            //     "Halo {$outwork->employee->user->name}, pengajuan insentif Anda DITOLAK. Silakan cek catatan di sini: " . route('portal::outwork.submission.show', $outwork->id),
            //     $outwork->employee->user
            // ));
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }
}
