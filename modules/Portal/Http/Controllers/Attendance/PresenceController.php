<?php

namespace Modules\Portal\Http\Controllers\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Enums\WorkLocationEnum;
use Modules\Core\Models\CompanyMoment;
use Modules\HRMS\Enums\WorkShiftEnum;
use Modules\HRMS\Models\EmployeeScanLog;
use Modules\HRMS\Models\EmployeeTeacherScanLog;
use Modules\HRMS\Enums\TransferTypeEnum;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Core\Enums\PositionTypeEnum;
use Modules\HRMS\Models\EmployeeTransferTeacher;
use Modules\HRMS\Enums\TeacherShiftEnum;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isTeacher = false;
        $isJakartaParentTeacher = false;

        if($request->user()->employee->position->position->type->value == PositionTypeEnum::BACKOFFICE->value && 
        $request->user()->employee->position->position->dept_id == 7){
            $isJakartaParentTeacher = true;
        }

        $today = now()->format('Y-m-d');
        
        $type = $request->user()->employee->position->position->type;
        if (in_array($type, [
            PositionTypeEnum::TEACHER,
            PositionTypeEnum::TEACHERJAKARTA
        ])) {
            $isTeacher = true;
        }

        $employee = $request->user()->employee;
        $type     = $employee->position->position->type;

        $transfer = EmployeeTransferTeacher::where('empl_id', $employee->id)
            ->whereIn('to', [TransferTypeEnum::JAKARTA, TransferTypeEnum::JOGJA])
            ->latest()
            ->first();

        $activeLocation = (
            $transfer &&
            now()->between(
                \Carbon\Carbon::parse($transfer->start_at)->startOfDay(),
                \Carbon\Carbon::parse($transfer->end_at)->endOfDay()
            )
        )
            ? $transfer->to
            : (
                $type === PositionTypeEnum::TEACHER
                    ? TransferTypeEnum::JOGJA
                    : ($type === PositionTypeEnum::TEACHERJAKARTA
                        ? TransferTypeEnum::JAKARTA
                        : null)
            );

        $isJogjaTeacher   = $activeLocation === TransferTypeEnum::JOGJA;
        $isJakartaTeacher = $activeLocation === TransferTypeEnum::JAKARTA;

        $isToJogja   = $type === PositionTypeEnum::TEACHERJAKARTA && $isJogjaTeacher;
        $isToJakarta = $type === PositionTypeEnum::TEACHER        && $isJakartaTeacher;

        $month = Carbon::parse($request->get('month', now()));
        $start_at = Carbon::parse($month)->startOfMonth();
        $end_at = Carbon::parse($month)->endOfMonth();

        $location = WorkLocationEnum::select($request->get('type'));
        $workshifts = WorkShiftEnum::cases();
        $workshiftsTeacher = TeacherShiftEnum::cases();

        $now = now()->format('H:i');

        $activeShift = TeacherShiftEnum::currentShift($now);
        $nextShift   = TeacherShiftEnum::nextShift($now);
        $moments = CompanyMoment::holiday()->whenMonthOfYear($month)->get()->groupBy('date');

        $employee = $request->user()->employee;
        
        $alreadyPresence = false;
        $alreadyPresenceTeacher = false;
        
        if ($activeShift) {
            $start = \Carbon\Carbon::parse($activeShift->activeStartTime()[0])->setDateFrom(now());
            $end   = \Carbon\Carbon::parse($activeShift->activeEndTime()[0])->setDateFrom(now());

            if ($end <= $start) {
                $end->addDay();
            }

            $alreadyPresence = EmployeeTeacherScanLog::where('empl_id', $employee->id)
                ->whereBetween('created_at', [$start, $end])
                ->exists();
        }

        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();
        $hour = (int)$now->format('H');

        $alreadyTeacherPresence = false;

        if ($hour >= 6 && $hour < 8) {
            $alreadyTeacherPresence = EmployeeScanLog::where('empl_id', $employee->id)
                ->whereBetween('created_at', [
                    $today . ' 06:00:00', 
                    $today . ' 08:00:00'
                ])
                ->exists();
        } 
        elseif ($hour >= 16) {
            $alreadyTeacherPresence = EmployeeScanLog::where('empl_id', $employee->id)
                ->where('created_at', '>=', $today . ' 16:00:00')
                ->exists();
        }

        $schedule = $employee->schedules()->whenMonth($month->format('Y-m'))->first();
        //$scanlogs = $employee->scanlogs()->where('created_at', 'like', $month->format('Y-m-') . '%')->groupBy(fn($log) => $log->created_at->format('Y-m-d'));
        if ($request->position == 'teacher') {
            $scanlogs = EmployeeTeacherScanLog::where('empl_id', $employee->id)
                ->where('created_at', '>=', $start_at)
                ->where('created_at', '<=', $end_at)
                ->where('location', (string) $location->value)
                ->get()
                ->groupBy(fn($log) => $log->created_at->format('Y-m-d'));
        } else {
            $scanlogs = EmployeeScanLog::where('empl_id', $employee->id)
                ->where('created_at', '>=', $start_at)
                ->where('created_at', '<=', $end_at)
                ->where('location', (string) $location->value)
                ->get()
                ->groupBy(fn($log) => $log->created_at->format('Y-m-d'));
        }

        $last_scan = isset($scanlogs[date('Y-m-d')]) ? $scanlogs[date('Y-m-d')]->last() : false;

        $current_schedule = $employee->schedules()->whenMonth(date('Y-m'))->first();

        // Modified
        $vacations = $employee->vacations()
            ->with('quota.category')
            ->whereExtractedDatesBetween($month->startOfMonth()->format("Y-m-d"), $month->endOfMonth()->format("Y-m-d"))
            ->get()
            ->filter(fn($vacation) => $vacation->hasAllApprovableResultIn('APPROVE'))
            ->pluck('dates')
            ->filter(fn($date) => empty($date->first()['cashable']))->flatten(1)->groupBy('d')->flatten(1)->unique();

        return view('portal::attendance.presence.index', compact(
            'month', 
            'location', 
            'workshifts', 
            'employee', 
            'moments', 
            'schedule', 
            'last_scan', 
            'scanlogs', 
            'current_schedule', 
            'vacations', 
            'isTeacher', 
            'isJogjaTeacher', 
            'isJakartaTeacher',
            'isToJogja',
            'isToJakarta',
            'isJakartaParentTeacher',
            'workshiftsTeacher',
            'activeShift',
            'nextShift',
            'alreadyPresence',
            'alreadyTeacherPresence',
            'transfer'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        $position = $request->position;
        
        if (($employee->position->position->type == PositionTypeEnum::TEACHER || 
        $employee->position->position->type == PositionTypeEnum::TEACHERJAKARTA) && $position == null) {
            $input = new EmployeeTeacherScanLog([
                'empl_id'         => $employee->id,
                'latlong'         => json_decode($request->input('latlong'), true),
                'location'         => $request->input('location'),
                'ip'             => getClientIp(),
                'user_agent'    => $request->server('HTTP_USER_AGENT')
            ]);
        } else {
            $input = new EmployeeScanLog([
                'empl_id'         => $employee->id,
                'latlong'         => json_decode($request->input('latlong'), true),
                'location'         => $request->input('location'),
                'ip'             => getClientIp(),
                'user_agent'    => $request->server('HTTP_USER_AGENT')
            ]);
        }

        if ($input->save()) {
            return redirect()->next()->with('success', 'Terima kasih telah melakukan scan presensi <strong>' . WorkLocationEnum::tryFrom($request->input('location'))?->name . '</strong> hari ini, semangat!');
        }

        return redirect()->fail();
    }
}
