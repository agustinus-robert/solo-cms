<?php

namespace Modules\Core\Http\Controllers\Company\Salary;

use Illuminate\Http\Request;
use Modules\Core\Models\CompanySalarySlip;
use Modules\Core\Models\CompanySalaryTemplate;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\Core\Http\Requests\Company\Salary\Template\StoreRequest;
use Modules\Core\Http\Requests\Company\Salary\Template\UpdateRequest;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Repositories\CompanySalaryTemplateRepository;

use function PHPUnit\Framework\returnSelf;

class TemplateController extends Controller
{
    use CompanySalaryTemplateRepository;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('core::company.salaries.templates.index', [
            'templates'      => CompanySalaryTemplate::whenTrashed($request->get('trash'))->paginate($request->get('limit', 10)),
            'template_count' => CompanySalaryTemplate::count(),
        ]);
    }

    /* *
     * Create new resource
     */
    public function create()
    {
        $defaults = collect(setting('cmp_payroll_default_components'));
        return view('core::company.salaries.templates.create', [
            'defaults' => $defaults,
            'slips' => CompanySalarySlip::with('categories.components')->get(),
            'items' => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($request->input('as_template')) {
            setting_set('cmp_payroll_default_components', $request->transformed()->toArray()['components']);
        }

        if ($request->input('as_template_feastday')) {
            setting_set('cmp_payroll_default_feastday_components', $request->transformed()->toArray()['components']);
        }

        if ($request->input('as_template_postyear')) {
            setting_set('cmp_payroll_default_postyear_components', $request->transformed()->toArray()['components']);
        }

        if ($salary = $this->storeCompanySalaryTemplate($request->transformed()->toArray(), $request->user())) {
            return redirect()->next()->with('success', 'Template <strong>' . $salary->name . '</strong> telah berhasil dibuat.');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanySalaryTemplate $template)
    {
        return view('core::company.salaries.templates.show', [
            'template'   => $template,
            'components' => collect($template['components']),
            'slips'      => CompanySalarySlip::with('categories.components')->get(),
            'items'      => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanySalaryTemplate $template, UpdateRequest $request)
    {
        if ($template = $this->updateCompanySalaryTemplate($template, $request->transformed()->toArray(), $request->user())) {

            return redirect()->next()->with('success', 'Template gaji dengan nama <strong>' . $template->name . '</strong> telah berhasil diperbarui.');
        }
        return redirect()->fail();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanySalaryTemplate $template, Request $request)
    {
        if ($template = $this->destroyCompanySalaryTemplate($template, $request->user())) {

            return redirect()->next()->with('success', 'Template gaji dengan nama <strong>' . $template->name . '</strong> telah berhasil dihapus.');
        }
        return redirect()->fail();
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(CompanySalaryTemplate $template, Request $request)
    {
        if ($template = $this->restoreCompanySalaryTemplate($template, $request->user())) {

            return redirect()->next()->with('success', 'Template gaji dengan nama <strong>' . $template->name . '</strong> telah berhasil dipulihkan.');
        }
        return redirect()->fail();
    }

    public function sync(Request $request)
    {
        $this->authorize('store', CompanySalaryTemplate::class);
        $componenet = setting('cmp_payroll_default_components');
        $componenet13 = setting('cmp_payroll_default_postyear_components');
        $componenetTHR = setting('cmp_payroll_default_feastday_components');

        $template = new CompanySalaryTemplate([
            'kd' => 'gaji-bulan',
            'name' => 'Gaji Bulan',
            'components' => $componenet,
            'meta' => null
        ]);

        $template2 = new CompanySalaryTemplate([
            'kd' => 'template-slip-gaji-ke-13',
            'name' => 'Template Slip Gaji ke-13',
            'components' => $componenet13,
            'meta' => null
        ]);

        $template3 = new CompanySalaryTemplate([
            'kd' => 'template-thr',
            'name' => 'Template THR',
            'components' => $componenetTHR,
            'meta' => null
        ]);

        if ($template->save() && $template2->save() && $template3->save()) {
            return redirect()->next()->with('success', 'Data template gaji telah berhasil disimpan.');
        }
        return redirect()->fail();
    }
}
