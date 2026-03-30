<?php

namespace Modules\Finance\Http\Controllers\Summary;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Core\Enums\PositionTypeEnum;
use Modules\Core\Enums\WorkLocationEnum;
use Modules\Core\Models\CompanyDepartment;
use Modules\HRMS\Models\EmployeeTransferTeacher;
use Modules\Core\Models\CompanyMoment;
use Modules\Core\Models\CompanyPosition;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\Core\Enums\VacationTypeEnum;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeePosition;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\Administration\Http\Controllers\Controller;
use Modules\Administration\Http\Requests\Summary\Attendance\StoreRequest;
use Modules\Administration\Http\Requests\Summary\Attendance\UpdateRequest;
use Modules\HRMS\Models\EmployeeRecapSubmission;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\Administration\Http\Requests\Service\Teacher\SubmissionUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('access', EmployeeRecapSubmission::class);
        $user     = $request->user();
        $employee = $user->employee->load('position.position.children');
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at   = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        $departments = CompanyDepartment::whereIn(
            'id',
            CompanyPosition::whereIn('type', [PositionTypeEnum::TEACHER, PositionTypeEnum::TEACHERJAKARTA])
                ->pluck('dept_id')
                ->unique()
                ->toArray()
        )->visible()
        ->with(['positions' => fn($poss) => 
            $poss->whereIn('type', [PositionTypeEnum::TEACHER, PositionTypeEnum::TEACHERJAKARTA])
        ])->get();

        $summaries = EmployeeRecapSubmission::whereType(DataRecapitulationTypeEnum::HONOR)->whereStrictPeriodIn($start_at, $end_at)->get();

        //   ->whereHas('position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))

        $employees = Employee::with('user', 'contract.position.position')
            ->whenPositionOfDepartment($request->get('department'), $request->get('position'))
            ->whereHas('position.position', fn($q) => 
                $q->whereIn('type', [
                    PositionTypeEnum::TEACHER->value, 
                    PositionTypeEnum::TEACHERJAKARTA->value
                ])
            )
            ->search($request->get('search'))
            ->paginate($request->get('limit', 10));
            
        return view('finance::summary.teacher.index', compact('start_at', 'end_at', 'departments', 'summaries', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = Employee::findOrFail($request->get('employee'));

        $userNow = $request->user()->employee->position->position_id;

        $workHour = $employee->getMeta('default_workhour');

        foreach (WorkLocationEnum::cases() as $location) {
            $locations[$location->value] = $location->name;
        }

        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        $leaves = $employee->leaves()->with('approvables')->whereExtractedDatesBetween($start_at, $end_at)->get()->filter(fn($leave) => $leave->hasAllApprovableResultIn('APPROVE'))->unique('id');
        $vacations = $employee->vacations()->with('approvables', 'quota.category')->whereExtractedDatesBetween($start_at, $end_at)->get()->filter(fn($vacation) => $vacation->hasAllApprovableResultIn('APPROVE'))->unique('id');
        $overtimes = $employee->overtimes()->with('approvables')->whereExtractedDatesBetween($start_at, $end_at)->get()->filter(fn($vacation) => $vacation->hasAllApprovableResultIn('APPROVE'))->unique('id')->flatMap(fn($overtime) => $overtime->dates->map(
            fn($date) => [
                'date' => $date['d'],
                'start_at' => Carbon::parse($date['d'] . ' ' . $date['t_s']),
                'end_at' => Carbon::parse($date['d'] . ' ' . $date['t_e'])
            ]
        ))->filter(fn($overtime) => $start_at->lte($overtime['date']) && $end_at->gte($overtime['date']));

        $moments = CompanyMoment::holiday()->whereBetween('date', [$start_at, $end_at])->get();

        $scanlogs = $employee->teachingscanlogs()->whereBetween('created_at', [$start_at, $end_at])->groupBy(fn($log) => $log->created_at->format('Y-m-d'));

        $schedules = $employee->schedulesTeachers()->wherePeriodIn($start_at, $end_at)->get()->each(function ($schedule) use ($scanlogs) {
            $schedule->entries = $schedule->getEntryLogs($scanlogs);
            return $schedule;
        });

        $entries = $schedules->pluck('entries')->mapWithKeys(fn($k) => $k)
            ->filter(fn($v, $k) => $start_at->lte(Carbon::parse($k)) && $end_at->gte(Carbon::parse($k)));

        $hourReguler = 0;
        $hourExtra = 0;
        $countPresenceExtra = [];

        foreach ($entries as $hours => $hour) {
            foreach ($hour as $shifts => $shift) {
                if ($shift->shift->value < 5) {
                    $in = Carbon::parse($shift->schedule[0]->toTimeString());
                    $out = Carbon::parse($shift->schedule[1]->toTimeString());

                    $diffInMinutes = $in->diffInMinutes($out);
                    $hourReguler += $diffInMinutes / 60;
                    $defaultTime = Carbon::createFromTime(8, 0, 0);

                    if ($shift->shift->value == 1 && $in->lessThan($defaultTime)) {
                        $extraMinutes = $in->diffInMinutes($defaultTime);
                        $hourExtra += $extraMinutes / 60;
                        $hourReguler -= $extraMinutes / 60;
                    }
                } else if ($shift->shift->value == 5) {
                    $in = Carbon::parse($shift->schedule[0]->toTimeString());
                    $countPresenceExtra[] = $in;
                    $out = Carbon::parse($shift->schedule[1]->toTimeString());
                    $diffInMinutes = $in->diffInMinutes($out);
                    $hourExtra += $diffInMinutes / 60;
                }
            }
        }

        $workDays = $start_at->diffInDaysFiltered(function (Carbon $date) use ($moments) {
            return $date->isWeekday() && !in_array($date->format('Y-m-d'), ($moments->pluck('date')->toArray()));
        }, $end_at);

        // dd($entries->flatten(1));
        $presences = $entries->flatten(1);
        //->filter(function ($e) {
        //  return $e->bool === true;
        // });
        $presencesTotal = $presences->unique('date');


        $adtDays = count($countPresenceExtra);
        $overtime_days = $presences->count() >= $workDays ? $presences->take(($presences->count() - $workDays) * -1) : collect([]);
        $overtime_holidays = $entries->flatten()->whereIn('date', $moments->pluck('date'))->values();

        /*
            rumus perhitungan untuk pengajar atau guru
        */

        /*
            RUMUS UNTUK CEK JIKA BEBAN MENGAJAR KURANG DARI JAM MINIMAL PENGAJARAN
            cek dahulu apakah $hourTotal bisa melebihi beban mengajar, dimana rumusnya adalah
            jam reguler + jam extra
            jika kurang dari beban mengajar tambahkan jam reguler dan extra sampai mencukupi beban mengajar
        */
        $extraOver = 0;
        $hourTotal = $hourExtra + $hourReguler;

        if ($hourReguler > $workHour) {
            /*
                jika dari shift 1-4 tesebut melebihi beban mengajar

                maka didapatkan kelebihan mengajar
            */

            $extraOver = $hourReguler - $workHour;
            $hourTotal = $workHour;
        } else if ($hourTotal > $workHour) {
            /*
                setelah ditambahkan dari shift 1-4 + extra mengajar maka akan menghasilkan
                KELEBIHAN MENGAJAR EXTRA
            */
            $hourExtra = $hourTotal - $workHour;
            $hourTotal = $workHour;
        } else if ($hourTotal < $workHour) {
            $hourExtra = 0;
        }

        /*
            akhir rumus perhitungan
        */

        return view('administration::summary.create', compact('employee', 'start_at', 'end_at', 'locations', 'leaves', 'vacations', 'overtimes', 'moments', 'schedules', 'entries', 'overtime_days', 'overtime_holidays', 'adtDays', 'workDays', 'presences', 'scanlogs', 'extraOver', 'hourExtra', 'workHour', 'hourReguler', 'hourTotal', 'userNow', 'presencesTotal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Employee $employee, StoreRequest $request)
    {
        $summaryTypeAtten = array_merge(
            $request->transformed()->toArray(),
            [
                'type' => DataRecapitulationTypeEnum::ATTENDANCE,
                'empl_id' => $request->employee,
            ]
        );

        unset($summaryTypeAtten['resultHour']);
        $summaryTypeHonor = array_merge(
            $request->transformed()->toArray(),
            [
                'type' => DataRecapitulationTypeEnum::HONOR,
                'empl_id' => $request->employee,
            ]
        );
        $summaryTypeHonor['result'] = [];
        $summaryTypeHonor['result'] = $summaryTypeHonor['resultHour'];
        $summaryTypeHonor = array_merge($summaryTypeHonor, $summaryTypeHonor['result']);

        if ($summaryTypeHonor['resultHour']['amount_total'] < $employee->getMeta('default_workhour')) {
            $summaryTypeHonor['result']['amount_total'] = $employee->getMeta('default_workhour');
            $summaryTypeHonor['result']['extrahour'] = $summaryTypeHonor['result']['extrahour'] * 0.35;
        }

        $summaryTypeHonor['result']['amount_real'] = $summaryTypeHonor['resultHour']['amount_total'];

        unset($summaryTypeHonor['resultHour']);

        $emp = $employee::find($request->employee);

        if ($request->user()->employee->position->position_id !== 9) {
            $insertType1 = EmployeeRecapSubmission::updateOrCreate(
                Arr::only($summaryTypeAtten, ['empl_id', 'type', 'start_at', 'end_at']),
                $summaryTypeAtten
            );

            $insertType8 = EmployeeRecapSubmission::updateOrCreate(
                Arr::only($summaryTypeHonor, ['empl_id', 'type', 'start_at', 'end_at']),
                $summaryTypeHonor
            );

            foreach (config('modules.core.features.services.recapteacher.approvable_steps', []) as $model) {
                if ($model['type'] == 'employee_position_by_kd') {
                    if ($approver = EmployeePosition::active()->whereHas('position', fn($position) => $position->whereIn('kd', $model['value']))->first()) {
                        $insertType1->createApprovable($approver);
                        $insertType8->createApprovable($approver);
                    }
                }
            }
        } else {
            $insertType1 = EmployeeDataRecapitulation::updateOrCreate(
                Arr::only($summaryTypeAtten, ['empl_id', 'type', 'start_at', 'end_at']),
                $summaryTypeAtten
            );

            $insertType8 = EmployeeDataRecapitulation::updateOrCreate(
                Arr::only($summaryTypeHonor, ['empl_id', 'type', 'start_at', 'end_at']),
                $summaryTypeHonor
            );
        }


        if ($insertType1 && $insertType8) {

            // $signs = array_map(function ($sign) use ($insertType1) {
            //     if ($sign == '%SELECTED_EMPLOYEE_USER_ID%') {
            //         return $insertType1->employee->user_id;
            //     } elseif (isset($sign['model'])) {
            //         $model = new $sign['model']();
            //         foreach ($sign['methods'] as $method) {
            //             $model = $model->{$method['w']}(...$method['p']);
            //         }
            //         return data_get($model, $sign['get']);
            //     }
            // }, config('modules.core.features.services.reacap_teacher.documentable_signs'));
            if ($request->user()->employee->position->position_id !== 9) {
                return redirect()->next()->with('success', 'Rekapitulasi presensi <strong>' . $emp->user->name . '</strong> sedang diajukan.');
            } else {
                $request->user()->log(
                    'melakukan rekapitulasi presensi <strong>' . $emp->user->name . '</strong>',
                    Employee::class,
                    $request->employee
                );

                return redirect()->next()->with('success', 'Rekapitulasi presensi <strong>' . $emp->user->name . '</strong> berhasil disimpan.');
            }
        }

        return redirect()->fail();
    }

    /* *
     * edit recaps
     */
    public function show($teaching, Request $request)
    {        
        $userNow = $request->user()->employee->position;
        $teachings = EmployeeRecapSubmission::where(['empl_id' => $teaching, 'start_at' => $request->start_at, 'end_at' => $request->end_at])->get();
        $filteredRecap = $teachings->filter(fn($item) => in_array($item->type, [DataRecapitulationTypeEnum::ATTENDANCE, DataRecapitulationTypeEnum::HONOR]))->values();

        $transfer = EmployeeTransferTeacher::where('empl_id', $teaching)
            ->where('start_date', '<=', $request->end_at) 
            ->where('end_date', '>=', $request->start_at)  
            ->first();

        $employee = Employee::with('user', 'contract', 'schedulesTeachers', 'vacations', 'leaves')->findOrFail($teaching);
        $workHour = $employee->getMeta('default_workhour');

        $moments = CompanyMoment::holiday()
            ->whereBetween('date', [$request->start_at, $request->end_at])
            ->get();

        $employeeVacations = $employee->vacations()->with('quota.category')->get();
        $employeeLeaves = $employee->leaves;


        $vacationSums = [];
        $dateVacations = [];
        $dateLeaves = [];
        foreach ($employeeVacations as $value) {
            foreach (json_decode($value->dates, true) as $val) {
                $dateVacations[$val['d']] = 1.5;
            }
        }

        foreach ($employeeLeaves as $value) {
            foreach (json_decode($value->dates, true) as $val) {
                $dateLeaves[$val['d']] = 1.5;
            }
        }

        foreach (VacationTypeEnum::cases() as $type) {
            $total = $employeeVacations
                ->filter(fn($vac) => $vac->quota?->category?->type->value === $type->value)
                ->sum(function ($vac) {
                    $dates = json_decode($vac->dates, true);
                    return is_array($dates) ? count($dates) : 0;
                });

            $vacationSums[strtolower($type->name)] = $total;
        }

        $results = ApprovableResultEnum::cases();
        $employee = Employee::findOrFail($teaching);

        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        foreach (WorkLocationEnum::cases() as $location) {
            $locations[$location->value] = $location->name;
        }


        $scanlogs = $employee->teachingscanlogs()->whereBetween('created_at', [$request->start_at, $request->end_at])->groupBy(fn($log) => $log->created_at->format('Y-m-d'));

        $schedules = $employee->schedulesTeachers()->wherePeriodIn($request->start_at, $request->end_at)->get()->each(function ($schedule) use ($scanlogs) {
            $schedule->entries = $schedule->getEntryLogs($scanlogs);
            return $schedule;
        });

        $scanlogdata = $employee->getAttendanceLogs(
            $start_at,
            $end_at
        );

        $entries = $schedules->pluck('entries')->mapWithKeys(fn($k) => $k)
            ->filter(fn($v, $k) => $start_at->lte(Carbon::parse($k)) && $end_at->gte(Carbon::parse($k)));

        $presencesTotal = $entries->unique('date');
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');
        $momentDates = $moments->pluck('date')->toArray();


        return view('finance::summary.teacher.edit', [
            'employee' => $employee,
            'attendance' => $filteredRecap[0],
            'teach' => $filteredRecap[1],
            'presencesTotal' => $presencesTotal,
            'scanlogdata' => $scanlogdata,
            'entries' => $entries,
            'locations' => $locations,
            'scanlogs' => $scanlogs,
            'userNow' => $userNow,
            'moments' => CompanyMoment::holiday()->whereBetween('date', [$request->start_at, $request->end_at])->get(),
            'results' => $results,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'employeeLeaves' => $employeeLeaves,
            'employeeVacationsSums' => $vacationSums,
            'employeeVacations' => $employeeVacations,
            'transfer' => $transfer
        ]);
    }

    public function submissionApprovals(SubmissionUpdateRequest $request)
    {
        $idAttendance = $request->input('id_attendance');
        $idTeaching = $request->input('id_teaching');

        $attendance = EmployeeRecapSubmission::with('approvables')
            ->whereIn('id', [$idAttendance, $idTeaching])
            ->get();

        $approvables = $attendance->flatMap->approvables;

        if ($approvables->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        foreach ($approvables as $approvable) {
            if (ApprovableResultEnum::APPROVE->value == (int) $request->result) {
                $approvable->update([
                    'result' => ApprovableResultEnum::APPROVE->value,
                    'reason' => $request->input('reason') ?? 'Diterima oleh sistem.',
                ]);

                $submission = EmployeeRecapSubmission::find($approvable->modelable_id);
                $submission->update(['validated_at' => now()]);

                if ((int) $request->result == ApprovableResultEnum::APPROVE->value && $submission) {
                    EmployeeDataRecapitulation::create([
                        'empl_id' => $submission->empl_id,
                        'type' => $submission->type,
                        'start_at' => $submission->start_at,
                        'end_at' => $submission->end_at,
                        'result' => $submission->result,
                    ]);
                }
            } else if (ApprovableResultEnum::REJECT->value == (int) $request->result) {
                $approvable->update([
                    'result' => ApprovableResultEnum::REJECT->value,
                    'reason' => $request->input('reason') ?? 'Ditolak oleh sistem.',
                ]);
            } else if (ApprovableResultEnum::REVISION->value == (int) $request->result) {
                $approvable->update([
                    'result' => ApprovableResultEnum::REVISION->value,
                    'reason' => $request->input('reason') ?? 'Ditolak oleh sistem.',
                ]);
            } else {
                $approvable->update([
                    'result' => $request->input('result'),
                    'reason' => $request->input('reason'),
                ]);
            }
        }

        return back()->with('success', 'Approval berhasil diperbarui.');
    }


    public function update(EmployeeDataRecapitulation $attendance, Employee $employee, UpdateRequest $request)
    {
        dd($attendance);
        if ($employee) {
            $attendance->fill($request->transformed()->toArray());
            if ($attendance->save()) {
                $request->user()->log('memperbarui rekapitulasi guru <strong>' . $attendance->employee->user->name . '</strong>', Employee::class, $attendance->empl_id);
                return redirect()->next()->with('success', 'Rekapitulasi guru <strong>' . $attendance->employee->user->name . '</strong> berhasil diperbarui.');
            }
            return redirect()->fail();
        }
        return redirect()->fail();
    }
}
