<?php

namespace Modules\Finance\Http\Controllers\Service\Deduction;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyDepartment;
use Modules\HRMS\Models\EmployeeDeduction;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\Finance\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\Service\Deduction\Manage\StoreRequest;
use Modules\Finance\Http\Requests\Service\Deduction\Manage\UpdateRequest;
use Modules\HRMS\Enums\DeductionTypeEnum;
use Modules\HRMS\Models\Employee;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start_at = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');

        return view('finance::service.deduction.manage.index', [
            'start_at'    => $start_at,
            'end_at'      => $end_at,
            'departments' => CompanyDepartment::visible()->with('positions')->get(),
            'employees'  => Employee::with(['user', 'contract.position.position', 'dataRecapitulations' => fn($recap) => tap(
                $recap->whereType(DataRecapitulationTypeEnum::DEDUCTION)->whereStrictPeriodIn($start_at, $end_at),
                fn($filtered) => $filtered->pluck('type')
            )])
                ->whenPositionOfDepartment($request->get('department'), $request->get('position'))
                ->whereHas('contract')
                ->search($request->get('search'))
                ->paginate($request->get('limit', 10))

        ]);
    }

    /**
     * Create resource
     */
    public function create(Request $request)
    {
        $start_at  = Carbon::parse($request->get('start_at', cmp_cutoff(0)->format('Y-m-d')) . ' 00:00:00');
        $end_at    = Carbon::parse($request->get('end_at', cmp_cutoff(1)->format('Y-m-d')) . ' 23:59:59');
        $employees = Employee::with('user')->get();
        $employee  = Employee::find($request->get('deduction'));
        $items     = !is_null($employee) ? $employee->salaryTemplate()->with(['items' => fn($s) => $s->whereIn('component_id', config('modules.finance.ref_deduction_item'))])->first()?->items ?? [] : [];

        return view('finance::service.deduction.manage.create', [
            'employees'  => $employees,
            'employee'   => $employee,
            'start_at'   => $start_at,
            'end_at'     => $end_at,
            'categories' => DeductionTypeEnum::cases(),
            'items'      => $items
        ]);
    }

    /**
     * Store a resource.
     */
    public function store(StoreRequest $request)
    {
        $resulter = $request->transform();
        $start_at = $request->start_at;
        $end_at = $request->end_at;

        $employee = Employee::find($resulter['empl_id']);

        $arr = [
            'empl_id' => (int) $resulter['empl_id'],
            'type' => DataRecapitulationTypeEnum::DEDUCTION,
            'start_at' => date('Y-m-d', strtotime($resulter['start_at'])),
            'end_at' => date('Y-m-d', strtotime($resulter['end_at'])),
            'result' => $resulter['result']
        ];

        $recap = $employee->dataRecapitulations()->create($arr);

        if ($recap->save()) {
            return redirect()->next()->with('success', 'Potongan atas nama <strong>' . $employee->user->name . '</strong> berhasil disimpan.');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $deduction, Request $request)
    {
        $start_at = $request->start_at;
        $end_at = $request->end_at;
        $status = 1;
        $userNow = $request->user()->employee->position;

        $results = ApprovableResultEnum::cases();
        $attendance = EmployeeDataRecapitulation::where([
            'empl_id' => $deduction->id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'type' => DataRecapitulationTypeEnum::DEDUCTION
        ])->first();

        return view('finance::service.deduction.manage.show', compact('start_at', 'end_at', 'deduction', 'userNow', 'attendance', 'results', 'status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Employee $deduction, UpdateRequest $request)
    {
        $start_at = $request->start_at;
        $end_at = $request->end_at;

        $attendance = EmployeeDataRecapitulation::where([
            'empl_id' => $deduction->id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'type' => DataRecapitulationTypeEnum::DEDUCTION
        ])->first();

        if ($attendance) {
            $attendance->update([
                'result' => $request->transform()['result']
            ]);

            return back()->with('success', 'Rekap potongan berhasil diperbarui!');
        }

        return back()->with('error', 'Rekap potongan gagal diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $deduction, Request $request)
    {
        $start_at = $request->start_at;
        $end_at = $request->end_at;

        $attendance = EmployeeDataRecapitulation::where([
            'empl_id' => $deduction->id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'type' => DataRecapitulationTypeEnum::DEDUCTION
        ])->first();

        if ($attendance) {
            $attendance->delete(); // Hapus data
            return back()->with('success', 'Data berhasil dihapus!');
        } else {
            return back()->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(EmployeeDeduction $deduction)
    {
        $deduction->restore();

        return redirect()->back()->with('success', 'Pengajuan reimbursement <strong>' . $deduction->employee->user->name . '</strong> berhasil dipulihkan');
    }
}
