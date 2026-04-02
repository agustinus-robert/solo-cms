<?php

namespace Modules\HRMS\Http\Controllers\Benefit\Insurance;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\Http\Requests\Benefit\Template\StoreRequest;
use Modules\HRMS\Http\Requests\Benefit\Template\UpdateRequest;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Models\CompanyInsurance;
use Modules\Core\Models\CompanySalarySlip;
use Modules\Core\Models\CompanySalarySlipComponent;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = CompanyInsurance::get()->pluck('kd')->toArray();

        return view('hrms::benefit.templates.index', [
            'templates'      => Setting::whereIn('key', $search)->paginate($request->get('limit', 10)),
            'template_count' => Setting::whereIn('key', $search)->count(),
        ]);
    }

    /* *
     * Create new resource
     */
    public function create()
    {
        $types = CompanyInsurance::get()
            ->map(fn($t) => ['label' => $t->name, 'value' => $t->kd])
            ->toArray();

        return view('hrms::benefit.templates.create', [
            'types' => $types,
            'slips' => CompanySalarySlip::whereIn('az', [1, 2])->get(),
            'items' => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->transformed()->toArray();
        $key  = $data['key'];
        $components = $data['components'];

        try {
            // Ambil old value untuk logging
            $oldValue = setting($key);

            // Simpan setting
            setting_set($key, $components);

            // Ambil kembali
            $newValue = setting($key);

            if (is_null($newValue)) {
                return redirect()
                    ->back()
                    ->with('error', 'Gagal menyimpan template. Silakan coba lagi.')
                    ->withInput();
            }

            /**
             * ===========================
             *  LOGGING KE USER->LOG()
             * ===========================
             */
            $request->user()->log(
                "Update setting {$key}",
                Setting::class
            );

            return redirect()
                ->next()
                ->with('success', 'Template <strong>' . e($key) . '</strong> telah berhasil dibuat.');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan internal. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $template)
    {
        return view('hrms::benefit.templates.show', [
            'template'   => $template,
            'components' => collect($template['value']),
            'slips'      => CompanySalarySlip::with('categories.components')->get(),
            'items'      => CompanySalarySlipComponent::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Setting $template, UpdateRequest $request)
    {
        $data = $request->transformed()->toArray();
        $key  = $data['key'];
        $components = $data['components'];

        try {
            // Ambil old value untuk logging
            $oldValue = $template->key;

            // Simpan setting
            setting_set($key, $components);

            // Ambil kembali
            $newValue = setting($key);

            if (is_null($newValue)) {
                return redirect()
                    ->back()
                    ->with('error', 'Gagal menyimpan template. Silakan coba lagi.')
                    ->withInput();
            }

            /**
             * ===========================
             *  LOGGING KE USER->LOG()
             * ===========================
             */
            $request->user()->log(
                "Update setting {$key}",
                Setting::class
            );

            return redirect()
                ->next()
                ->with('success', 'Template <strong>' . e($key) . '</strong> telah berhasil diperbarui.');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan internal. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $template)
    {
        return redirect()->fail();
    }
}
