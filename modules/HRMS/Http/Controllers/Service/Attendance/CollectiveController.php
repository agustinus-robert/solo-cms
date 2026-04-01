<?php

namespace Modules\HRMS\Http\Controllers\Service\Attendance;

use Illuminate\Http\Request;
use Modules\Core\Enums\PositionTypeEnum;
use Modules\Core\Models\CompanyMoment;
use Modules\HRMS\Enums\WorkShiftEnum;
use Modules\HRMS\Models\EmployeeContract;
use Modules\HRMS\Models\EmployeeSchedule;
use Modules\HRMS\Repositories\EmployeeScheduleRepository;
use Modules\HRMS\Http\Requests\Service\Attendance\Collective\StoreRequest;
use Modules\HRMS\Http\Controllers\Controller;
use Modules\HRMS\Models\Employee;

class CollectiveController extends Controller
{
    use EmployeeScheduleRepository;

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $month = $request->old('month', $request->get('month', date('Y-m')));
        $workshifts = WorkShiftEnum::cases();

        $dates = [];
        for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($month)), date('Y', strtotime($month))); $i++) {
            $dates[] = $month . '-' . str_pad($i, 2, 0, STR_PAD_LEFT);
        }

        $moments = CompanyMoment::holiday()->whenMonthOfYear($month)->get();
        $emplIds = [17, 18];

        $contracts = EmployeeContract::with([
                'employee.user',
                'position.position' => fn($w) => $w->with('department')
            ])
            ->active()
            ->get();


        $worktime_default = setting('cmp_empl_default_worktimes');

        return view('hrms::service.attendance.collective.create', compact('dates', 'workshifts', 'contracts', 'moments', 'worktime_default'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $dates = [];

        $dates = $request->input('dates');
        $filteredDates = [];

        foreach ($dates as $date => $shifts) {
            $validShifts = [];

            foreach ($shifts as $shift) {
                if (!empty($shift[0]) && !empty($shift[1])) {
                    $validShifts[] = $shift;
                }
            }

            if (count($validShifts) > 0) {
                $filteredDates[$date] = $validShifts;
            }
        }

        if (count($filteredDates) == 0) {
            return redirect()->back()->with('danger', 'Shift tidak boleh kosong pada alokasi jadwal');
        } else if (empty($request->input('empl_ids')) || empty($request->input('dates'))) {
            return redirect()->back()->with('danger', 'Belum ada karyawan yang dipilih untuk alokasi jadwal');
        }

        if (EmployeeSchedule::upsert($request->transformed()->toArray(), ['empl_id', 'start_at', 'end_at'], ['dates', 'workdays_count'])) {
            return redirect()->next()->with('success', 'Berhasil meregistrasikan ' . $count . ' jadwal kerja karyawan untuk periode ' . ($request->input('month')) . ' secara kolektif.');
        }
        return redirect()->fail();
    }
}
