<?php

namespace Modules\Portal\Http\Controllers\Loan;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Enums\LoanTenorEnum;
use Modules\Core\Models\CompanyLoanCategory;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Models\EmployeeLoan;
use Modules\HRMS\Models\EmployeePosition;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Loan\Submission\StoreRequest;
use Modules\Portal\Notifications\Loan\Submission\SubmissionNotification;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        // $templates = $employee->salaryTemplates()->count();
        $templates = 0;

        return view('portal::loan.submission.index', [
            'employee' => $employee->load('user'),
            'templates' => $templates,
            'approvers' => EmployeePosition::active()->whereHas('position', fn($position) => $position->whereIn('level', array_column(config('modules.finance.features.loans.approvable_steps'), 'value')))->get(),
            'loans' => $employee->loans()->with('installments', 'category', 'childrens')
                ->whereNull('parent_id')
                ->whenOnlyPending($request->get('pending'))
                ->paidOff($request->get('paidoff', false))
                ->orderbyDesc('id')
                ->paginate($request->get('limit', 10)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if ($employee->salaryTemplates()->count() > 0) {
            $data = $employee->salaryTemplates()->latest()->where('name', 'like', '%bulan%')->active()->first()->items;
            if (!empty($data) && !is_null($data)) {
                return view('portal::loan.submission.create', [
                    'employee'      => $request->user()->employee,
                    'categories'    => CompanyLoanCategory::whereJsonContains('meta->visible', true)->whenOnlyPermanentEmployee($user->employee->permanent_at)->orderBy('type')->get()->sortBy('meta.az'),
                    'tenor_types'   => LoanTenorEnum::cases(),
                    'start_at'      => cmp_cutoff(1)->format('Y-m-d'),
                    'next_start_at' => cmp_cutoff(1)->addMonth(1)->format('Y-m-d'),
                    'main_salary'   => array_sum($this->getMainSalary($data)->toArray())
                ]);
            }
            return redirect()->back()->with('danger', 'Data belum lengkap atau belum sesuai, silakan hubungi HRD atau Keuangan!');
        }
        return redirect()->back()->with('danger', 'Data belum lengkap atau belum sesuai, silakan hubungi HRD atau Keuangan!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $employee = $request->user()->employee;

        $input = $request->transform();

        $data = ['description', 'tenor', 'tenor_by', 'submission_at', 'start_at', 'empl_id', 'amount_total', 'ctg_id', 'meta'];

        if ($loan = $employee->loans()->create(Arr::only($input, $data))) {
            if ($input['interest']) {
                // Create new loan as interest based parent loan
                $new_interest = array_merge(Arr::only($input['interest'], $data), [
                    'parent_id' => $loan->id
                ]);
                // store interest
                if ($interest = $employee->loans()->create($new_interest)) {
                    $interest->installments()->createMany($input['interest']['installments']);
                    // Assign approvable based approvable_steps configuration
                    $this->attachApprovable($employee, $interest);
                }
            }
            if ($loan->installments()->createMany($input['installments'])) {
                // Assign approvable based approvable_steps configuration
                $this->attachApprovable($employee, $loan);
            }
            return redirect()->route('portal::loan.submission.index')->with('success', isset($approvable) ? 'Pengajuan pinjaman sudah terkirim, silakan tunggu notifikasi selanjutnya dari atasan!' : 'Pengajuan sudah tersimpan dan sudah disetujui otomatis oleh sistem, terima kasih!');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeLoan $loan, Request $request)
    {
        $user = $request->user();

        return view('portal::loan.submission.show', [
            'user' => $user,
            'employee' => $user->employee,
            'loan' => $loan->load('approvables.userable.position', 'category', 'installments.transactions')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeLoan $loan)
    {
        if ($approvable = $loan->approvables()->whereIn('result', [ApprovableResultEnum::PENDING])->orderBy('level')->first()) {
            $loan->delete();
            return redirect()->route('portal::loan.submission.index')->with('success', 'Pengajuan telah dibatalkan');
        }
        return redirect()->route('portal::loan.submission.index')->with('danger', 'Pengajuan tidak bisa dibatalkan, mohon hubungi bagian keuangan!');
    }

    public function attachApprovable($empl, $data)
    {
        foreach (config('modules.finance.features.loans.approvable_steps', []) as $index => $model) {
            if ($model['type'] == 'parent_position_level' && $model['current_dept']) {
                $empl->position->position->parents->where('level.value', $model['value'])->where('dept_id', $empl->position->position->dept_id)->each(
                    fn($position) => $data->createApprovable($position->employeePositions()->active()->first())
                );
            }
            if ($model['type'] == 'parent_position_level' && !$model['current_dept']) {
                $empl->position->position->parents->where('level.value', $model['value'])->each(
                    fn($position) => $data->createApprovable($position->employeePositions()->active()->first())
                );
            }
            if ($model['type'] == 'employee_position_by_kd') {
                if ($approver = EmployeePosition::active()->whereHas('position', fn($position) => $position->whereIn('kd', $model['value']))->first()) {
                    $data->createApprovable($approver);
                }
            }
        }

        // Handle notifications
        if ($approvable = $data->approvables()->orderBy('level')->first()) {
            $approvable->userable->getUser()->notify(new SubmissionNotification($data, null));
        }
    }

    public function getMainSalary($items)
    {
        $gp = CompanySalarySlipComponent::where('kd', 'gaji-pokok')->first()->id;
        $tj = CompanySalarySlipComponent::where('kd', 'tj-jabatan')->first()->id;
        $tp = CompanySalarySlipComponent::where('kd', 'tj-profesi')->first()->id;
        $tk = CompanySalarySlipComponent::where('kd', 'tj-keahlian')->first()->id;
        $array = [$gp, $tj, $tp, $tk];

        return $items->whereIn('component_id', $array)->pluck('amount') ?: 0;
    }

    public function checkApprovable($empl)
    {
        foreach (config('modules.finance.features.loans.approvable_steps', []) as $index => $model) {
            if ($model['type'] == 'parent_position_level' && $model['current_dept']) {
                $data[$index] = $empl->position->position->parents->where('level.value', $model['value'])->where('dept_id', $empl->position->position->dept_id)->first();
            }
            if ($model['type'] == 'parent_position_level' && !$model['current_dept']) {
                $data[$index] = $empl->position->position->parents->where('level.value', $model['value']);
            }
            if ($model['type'] == 'employee_position_by_kd') {
                $data[$index] = EmployeePosition::active()->whereHas('position', fn($position) => $position->whereIn('kd', $model['value']))->first();
            }
        }
        return $data;
    }
}
