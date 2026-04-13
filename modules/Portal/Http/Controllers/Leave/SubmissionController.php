<?php

namespace Modules\Portal\Http\Controllers\Leave;

use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Core\Enums\PositionLevelEnum;
use Modules\Core\Models\CompanyLeaveCategory;
use Modules\HRMS\Models\EmployeeLeave;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Leave\Submission\StoreRequest;
use App\Notifications\GlobalGenericNotification;
use Modules\Portal\Notifications\Leave\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Leave\Submission\CanceledNotification;

class SubmissionController extends Controller
{
    private function sendSubmissionNotification($targetUser, $type = 'store')
    {
        try {
            if (!$targetUser || !$targetUser->id) {
                \Log::warning("Notif Izin: Target user null.");
                return;
            }

            $user = auth()->user();
            $isCancel = ($type === 'cancel');

            $targetUser->sendSystemNotification([
                'user_id_target' => $targetUser->id,
                'title'          => $isCancel ? 'Pengajuan Dibatalkan' : 'Persetujuan Izin Baru',
                'message'        => $isCancel
                    ? "Membatalkan pengajuan izin yang sebelumnya diajukan."
                    : "Mengajukan izin baru dari {$user->name}. Silakan periksa detailnya.",
                'link'           => $isCancel ? '#' : route('hrms::service.leave.manage.index'),
                'icon'           => $isCancel ? 'bx bx-x-circle' : 'bx bx-file',
                'color'          => $isCancel ? 'danger' : 'warning',
                'sender_id'      => $user->id
            ]);

        } catch (\Exception $e) {
            \Log::error("Realtime Leave Notification Error: " . $e->getMessage());
        }
    }
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$employee = $request->user()->employee;

		$leaves = $employee->leaves()
			->withTrashed()
			->with('approvables.userable.position')
			->search($request->get('search'))
			->whenPeriod($request->get('start_at'), $request->get('end_at'))
			->latest()
			->paginate($request->get('limit', 10));

        $year = date('Y');
        $leaves_this_year_count = $employee->leaves()
            ->whereRaw("jsonb_path_exists(dates::jsonb, '$[*] ? (@.d >= \"$year-01-01\" && @.d <= \"$year-12-31\")')")
            ->whereApproved()
            ->count();

		return view('portal::leave.submission.index', compact('employee', 'leaves', 'leaves_this_year_count'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Request $request)
	{
		$employee = $request->user()->employee;

		$categories = CompanyLeaveCategory::with('children')->whereNull('parent_id')->get();

		return view('portal::leave.submission.create', compact('employee', 'categories'));
	}

	public function store(StoreRequest $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $leave = $employee->leaves()->create($request->transform());

        if ($user->hasRole('administrator')) {
            return redirect()->route('portal::leave.submission.index')
                ->with('success', 'Pengajuan Administrator otomatis disetujui oleh sistem.');
        }

        $parentPositions = $employee->position->position->parents;

        if ($parentPositions->isNotEmpty()) {
            foreach ($parentPositions as $parentPosition) {
                $approverEmployee = $parentPosition->employeePositions()
                    ->active()
                    ->first();

                if ($approverEmployee) {
                    $leave->createApprovable($approverEmployee);
                }
            }
        }

        if ($approvable = $leave->approvables()->orderBy('level')->first()) {
            $targetUser = $approvable->userable->employee->user;
            $this->sendSubmissionNotification($targetUser, 'store');
            $message = 'Pengajuan izin sudah terkirim ke atasan.';
        } else {
            $message = 'Pengajuan sudah tersimpan dan disetujui otomatis (tidak ada hirarki atasan).';
        }

        return redirect()->route('portal::leave.submission.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeLeave $leave)
    {
        $employeeName = $leave->employee->user->name;
        $leave->delete();

        $approvable = $leave->approvables()->orderBy('level')->first();

        if ($approvable && $approvable->userable) {
            $targetUser = $approvable->userable->employee->user;

            $this->sendSubmissionNotification($targetUser, 'cancel');
        }

        return redirect()->route('portal::leave.submission.index')->with('success', 'Pengajuan telah dibatalkan dan kami telah mengirim notifikasi ke atasan!');
    }
}
