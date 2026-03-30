<?php

namespace Modules\Portal\Http\Controllers\Schedule;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Models\CompanyMoment;
use Modules\HRMS\Enums\WorkShiftEnum;
use Modules\HRMS\Models\Employee;
use Modules\Portal\Http\Controllers\Controller;
use Modules\HRMS\Repositories\EmployeeScheduleRepository;
use Modules\Portal\Http\Requests\Schedule\StoreRequest;

class NonstaffController extends Controller
{
	use EmployeeScheduleRepository;

	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$month 		= Carbon::parse($request->get('month', now()));
		$start_at 	= $month->copy()->startOfMonth()->format("Y-m-d");
		$end_at 	= $month->copy()->endOfMonth()->format("Y-m-d");
		$workshifts = WorkShiftEnum::cases();
		$moments 	= CompanyMoment::holiday()->whenMonthOfYear($month)->get()->groupBy('date');

		$employees = Employee::with(['user', 'contract.position', 'contract.schedules' => fn($sc) => $sc->whenMonth($month->format('Y-m'))])
			->whereHas('contract.position', fn($pos) => $pos->whereIn('position_id', [21, 22, 43]))->get();

		$references = $employees->mapToGroups(function ($i) {
			return [$i->contract->id => $i];
		});

		foreach ($employees as $empl => $nonstafs) {
			$_nonstaf[] = $nonstafs->contract->schedules->first();
			$nonstaf = array_filter($_nonstaf);

			$result = [];
			foreach ($nonstaf as $key => $value) {
				$dates[$key] = $value['dates'];

				$data = [];
				foreach ($dates[$key] as $dt => $shifts) {
					foreach ($shifts as $_key => $shift) {
						if (array_filter($shift)) {
							$data[$dt][$_key] = (object) [
								'c_id' => $value['contract_id'],
								'e_id' => $references[$value['contract_id']]->first()->id,
								'name' => $references[$value['contract_id']]->first()->user->username,
								'date' => Carbon::parse($shift[0])->format('Y-m-d'),
								'shift' => $_key + 1,
								'in' => Carbon::parse($shift[0])->format('H:i'),
								'out' => Carbon::parse($shift[1])->format('H:i'),
							];
						}
					}
				}
				$result[$key]['dates'] = $data;
			}
		}
		$results = collect($result)->pluck('dates')->flatten();

		return view('portal::schedules.nonstaf.index', compact('month', 'workshifts', 'moments', 'results',));
	}

	/**
	 * create a resource.
	 */
	public function create(Request $request)
	{
		$employees = Employee::with(['user', 'contract.position', 'contract.schedules' => fn($sc) => $sc->whenMonth(now()->format('Y-m'))])
			->whereHas('contract.position', fn($pos) => $pos->whereIn('position_id', [21, 22, 43]))->get();

		return view('portal::schedules.nonstaf.create', compact('employees'));
	}

	/**
	 * store a resource.
	 */
	public function store(StoreRequest $request)
	{
		return $request->transformed();
	}
}
