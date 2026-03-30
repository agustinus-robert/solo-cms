<?php

namespace Modules\HRMS\Http\Controllers\Benefit\Insurance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\HRMS\Http\Controllers\Controller;
use Modules\HRMS\Http\Requests\Benefit\Template\StoreBpjsSettingRequest;
use Modules\HRMS\Models\EmployeeInsurance;

class DefaultSalaryController extends Controller
{
    public function store(StoreBpjsSettingRequest $request)
    {
        // Validasi input
        $validated = $request->validated();

        Log::info($validated);

        DB::beginTransaction();
        try {
            // Ambil nilai lama untuk audit
            $oldMax = setting_get('cmp_insurance_max_salary');
            $oldMin = setting_get('cmp_insurance_min_salary');
            $oldMaxPensiun = setting_get('cmp_insurance_tk_pensiun_max_salary');
            $oldMinTK = setting_get('cmp_insurance_tk_min_salary');
            $oldlimit = setting_get('cmp_insurance_kes_limit');

            // Simpan nilai baru
            setting_set('cmp_insurance_max_salary', $validated['max_salary']);
            setting_set('cmp_insurance_min_salary', $validated['min_salary']);
            setting_set('cmp_insurance_tk_pensiun_max_salary', $validated['max_tk_pensiun_salary']);
            setting_set('cmp_insurance_tk_min_salary', $validated['min_tk_salary']);
            setting_set('cmp_insurance_kes_limit', $validated['limit_salary']);

            // Clear related cache keys
            Cache::forget('bpjs_max_salary');
            Cache::forget('bpjs_min_salary');
            Cache::forget('bpjs_tk_max_salary');
            Cache::forget('bpjs_tk_pensiun_max_salary');
            Cache::forget('bpjs_tk_min_salary');
            Cache::forget('bpjs_kes_limit');

            DB::commit();

            $changes = [
                'cmp_insurance_max_salary' => [
                    'old' => $oldMax,
                    'new' => $validated['max_salary'],
                ],
                'cmp_insurance_min_salary' => [
                    'old' => $oldMin,
                    'new' => $validated['min_salary'],
                ],
                'cmp_insurance_tk_pensiun_max_salary' => [
                    'old' => $oldMaxPensiun,
                    'new' => $validated['max_tk_pensiun_salary'],
                ],
                'cmp_insurance_tk_min_salary' => [
                    'old' => $oldMinTK,
                    'new' => $validated['min_tk_salary'],
                ],
            ];

            foreach ($changes as $key => $value) {
                $request->user()->log(
                    "Update setting {$key}: {$value['old']} → {$value['new']}"
                );
            }

            // Logging perubahan
            Log::info('BPJS pension salary settings updated', [
                'user_id' => auth()->id(),
                'old' => [
                    'max_salary' => $oldMax,
                    'min_salary' => $oldMin,
                    'max_tk_pensiun_salary' => $oldMaxPensiun,
                    'min_tk_salary' => $oldMinTK,
                ],
                'new' => [
                    'max_salary' => $validated['max_salary'],
                    'min_salary' => $validated['min_salary'],
                    'max_tk_pensiun_salary' => $validated['max_tk_pensiun_salary'],
                    'min_tk_salary' => $validated['min_tk_salary'],
                ],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()
                ->next()
                ->with(
                    'success',
                    'Setting maksimal gaji BPJS Pensiun sebesar <strong>Rp' . Str::money($validated['max_salary'], '0', 'IDR') . '</strong> dan BPJS Kesehatan sebesar <strong>Rp' .  Str::money($validated['min_salary'], '0', 'IDR') . '</strong> berhasil disimpan.'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to update BPJS pension salary settings', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengaturan.');
        }
    }
}
