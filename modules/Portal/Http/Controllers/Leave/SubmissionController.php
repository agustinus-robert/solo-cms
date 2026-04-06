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
use Modules\Portal\Notifications\Leave\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Leave\Submission\CanceledNotification;

class SubmissionController extends Controller
{
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

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $parentPositions = $employee->position->position->parents;

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
            // $approvable->userable->getUser()->notify(new SubmissionNotification($leave, null));
            $message = 'Pengajuan izin sudah terkirim ke atasan.';
        } else {
            $message = 'Pengajuan sudah tersimpan dan disetujui otomatis (tidak ada hirarki atasan).';
        }

        return redirect()->route('portal::leave.submission.index')->with('success', $message);
    }

	/**
	 * Display the specified resource.
	 */
	public function show(EmployeeLeave $leave, Request $request)
	{
		$user = $request->user();
		$employee = $user->employee;

		$leave = $leave->load('approvables.userable.position');

		return view('portal::leave.submission.show', compact('user', 'employee', 'leave'));
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(EmployeeLeave $leave)
	{
		$leave->delete();

		// Handle notifications
		if ($approvable = $leave->approvables()->whereNotIn('result', [ApprovableResultEnum::PENDING])->orderBy('level')->first()) {
			$approvable->userable->employee->user->notify(new CanceledNotification($leave));
		}

		return redirect()->route('portal::leave.submission.index')->with('success', 'Pengajuan telah dibatalkan dan kami telah mengirim notifikasi ke atasan!');
	}
}
