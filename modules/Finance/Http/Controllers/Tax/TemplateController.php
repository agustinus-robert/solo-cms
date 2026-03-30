<?php

namespace Modules\Finance\Http\Controllers\Tax;

use Illuminate\Http\Request;
use App\Models\Setting;
use Modules\Finance\Http\Requests\Tax\Template\StoreRequest;
use Modules\Finance\Http\Requests\Tax\Template\UpdateRequest;
use Modules\Finance\Http\Requests\Tax\Template\ConfigRequest;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Models\CompanySalarySlip;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Enums\TaxObjectEnum;
use Modules\HRMS\Enums\TaxTypeEnum;

class TemplateController extends Controller
{
    private $slipIds = [1, 3];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = [TaxTypeEnum::MONTHLY->key(), TaxTypeEnum::TER->key()];
        return view('finance::tax.templates.index', [
            'templates'      => Setting::whereIn('key', $search)->paginate($request->get('limit', 10)),
            'template_count' => Setting::whereIn('key', $search)->count(),
            'objects'        => TaxObjectEnum::cases(),
            'configs'        => setting('cmp_pph_objective_percentage')
        ]);
    }

    /* *
     * Create new resource
     */
    public function create()
    {
        $slipIds = $this->slipIds;
        return view('finance::tax.templates.create', [
            'types' => [TaxTypeEnum::MONTHLY, TaxTypeEnum::TER],
            'slips' => CompanySalarySlip::whereIn('az', $slipIds)->get(),
            'items' => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        setting_set($request->transformed()->toArray()['key'], $request->transformed()->toArray()['components']);
        if (setting($request->transformed()->toArray()['key'])) {
            return redirect()->next()->with('success', 'Template <strong>' . $request->input('key') . '</strong> telah berhasil dibuat.');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $template)
    {
        $slipIds = $this->slipIds;
        return view('finance::tax.templates.show', [
            'template'   => $template,
            'components' => collect($template['value']),
            'slips'      => CompanySalarySlip::whereIn('az', $slipIds)->get(),
            'items'      => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Setting $template, UpdateRequest $request)
    {
        setting_set($request->transformed()->toArray()['key'], $request->transformed()->toArray()['components']);
        if (setting($request->transformed()->toArray()['key'])) {
            return redirect()->next()->with('success', 'Template <strong>' . $request->input('key') . '</strong> telah berhasil diperbarui.');
        }
        return redirect()->fail();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $template)
    {
        return redirect()->fail();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function config(ConfigRequest $request)
    {
        setting_set($request->transformed()->toArray()['key'], $request->transformed()->toArray()['components']);
        if (setting($request->transformed()->toArray()['key'])) {
            return redirect()->next()->with('success', 'Beban pph21 <strong>' . $request->input('key') . '</strong> telah berhasil disimpan.');
        }
        return redirect()->fail();
    }
}
