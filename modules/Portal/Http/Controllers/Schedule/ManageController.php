<?php

namespace Modules\Portal\Http\Controllers\Schedule;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Enums\PositionTypeEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Core\Models\CompanyMoment;
use Modules\HRMS\Enums\ObShiftEnum;
use Modules\HRMS\Enums\TeacherShiftEnum;
use Modules\HRMS\Enums\WorkShiftEnum;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeSchedule;
use Modules\HRMS\Repositories\EmployeeScheduleRepository;
use Modules\HRMS\Repositories\EmployeeRepository;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Schedule\StoreRequest;
use Modules\Portal\Http\Requests\Schedule\UpdateRequest;
use Modules\Portal\Notifications\Schedule\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Schedule\Manage\ApprovedNotification;
use Modules\Portal\Notifications\Schedule\Manage\RejectedNotification;

class ManageController extends Controller
{
    use EmployeeRepository, EmployeeScheduleRepository;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('access', EmployeeSchedule::class);

        $user     = $request->user();
        $employee = $user->employee->load('position.position.children');
        $month    = Carbon::parse($request->get('month', now()));

        $employees = Employee::with([
            'user.meta',
            'contract.position.position',
            'schedules' => fn($schedule) => $schedule->whenMonth($month),
        ])
            ->whereHas('position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whereHas('position.position', fn($p) => $p->whereNotIn('type', [PositionTypeEnum::BACKOFFICE]))
            ->search($request->get('search'))->whenTrashed($request->get('trash'))->paginate($request->get('limit', 10));

        $employee_count = $employees->count();

        return view('portal::schedules.manage.index', compact('user', 'employees', 'employee_count'));
    }

    /**
     * create a resource.
     */
    public function create(Request $request)
    {
        $this->authorize('store', EmployeeSchedule::class);

        $employee = Employee::findOrFail($request->get('employee'));
        $month    = Carbon::parse($request->get('month', now()));
        $start_at = $month->copy()->startOfMonth()->format('Y-m-d 01:00:01');
        $end_at   = $month->copy()->endOfMonth()->format('Y-m-d 23:59:59');
        $moments  = CompanyMoment::holiday()->whereBetween('date', [Carbon::parse($start_at)->format('Y-m-d'), Carbon::parse($end_at)->format('Y-m-d')])->get();
        $periods  = CarbonPeriod::create(Carbon::parse($start_at)->format('Y-m-d'), '1 day', Carbon::parse($end_at)->format('Y-m-d'));

        switch ($employee->position->position->type) {
            case PositionTypeEnum::BACKOFFICE:
                $workshifts = WorkShiftEnum::cases();
                break;

            case PositionTypeEnum::TEACHER:
                $workshifts = TeacherShiftEnum::cases();
                break;

            case PositionTypeEnum::NONSTAF:
            case PositionTypeEnum::SECURITY:
                $workshifts = ObShiftEnum::cases();
                break;

            default:
                $workshifts = [];
                break;
        }

        // Iterate over the period
        $dates = [];
        foreach ($periods as $key => $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return view('portal::schedules.manage.create', compact('employee', 'dates', 'workshifts', 'moments', 'start_at', 'end_at'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($schedule = $this->storeEmployeeSchedule($request->transformed()->toArray())) {

            // foreach (config('modules.core.features.services.schedules.approvable_steps', []) as $model) {
            //     if ($model['type'] == 'parent_position_level') {
            //         if ($position = $schedule->employee->position->position->parents->firstWhere('level.value', $model['value'])?->employeePositions()->active()->first()) {

            //             $schedule->createApprovable($position);
            //         }
            //     }
            // }

            return redirect()->next()->with('success', 'Jadwal kerja karyawan baru atas nama <strong>' . $schedule->employee->user->name . '</strong> berhasil dibuat.');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeSchedule $schedule, Request $request)
    {
        $this->authorize('update', $schedule);
        $month     = Carbon::parse($request->get('month', now()));
        $start_at  = $month->copy()->startOfMonth()->format('Y-m-d 01:00:01');
        $end_at    = $month->copy()->endOfMonth()->format('Y-m-d 23:59:59');
        $periods   = CarbonPeriod::create(Carbon::parse($start_at)->format('Y-m-d'), '1 day', Carbon::parse($end_at)->format('Y-m-d'));
        $moments   = CompanyMoment::holiday()->whereBetween('date', [Carbon::parse($start_at)->format('Y-m-d'), Carbon::parse($end_at)->format('Y-m-d')])->get();

        switch ($schedule->employee->position->position->type) {
            case PositionTypeEnum::BACKOFFICE:
                $workshifts = WorkShiftEnum::cases();
                break;

            case PositionTypeEnum::TEACHER:
                $workshifts = TeacherShiftEnum::cases();
                break;

            case PositionTypeEnum::NONSTAF:
            case PositionTypeEnum::SECURITY:
                $workshifts = ObShiftEnum::cases();
                break;

            default:
                $workshifts = [];
                break;
        }

        $dates = [];
        foreach ($periods as $key => $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return view('portal::schedules.manage.show', compact('schedule', 'workshifts', 'dates', 'moments', 'start_at', 'end_at'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeSchedule $schedule, UpdateRequest $request)
    {
        if ($schedule = $this->updateEmployeeSchedule($schedule, $request->transformed()->toArray())) {
            return redirect()->next()->with('success', 'Jadwal kerja karyawan baru atas nama <strong>' . $schedule->employee->user->name . '</strong> berhasil diperbarui.');
        }
        return redirect()->fail();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeSchedule $schedule)
    {
        $this->authorize('destroy', $schedule);

        if ($schedule = $this->destroyEmployeeSchedule($schedule)) {
            return redirect()->next()->with('success', 'Jadwal kerja karyawan baru atas nama <strong>' . $schedule->employee->user->name . '</strong> berhasil dihapus.');
        }
        return redirect()->fail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function approval(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $approvable->update($request->transformed()->toArray());

        // Handle notifications
        if ($request->input('result') == ApprovableResultEnum::APPROVE->value) {
            $approvable->modelable->employee->user->notify(new ApprovedNotification($approvable->modelable, $approvable));
            if ($superior = $approvable->modelable->approvables->sortBy('level')->filter(fn($a) => $a->level > $approvable->level)->first()) {
                $superior->userable->employee->user->notify(new SubmissionNotification($approvable->modelable, $approvable->userable));
            }
        }

        if ($request->input('result') == ApprovableResultEnum::REJECT->value) {
            $approvable->modelable->employee->user->notify(new RejectedNotification($approvable->modelable, $approvable));
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }
}
