<?php

namespace Modules\Finance\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Models\Employee;
use Illuminate\Support\Facades\Log;
use Modules\Account\Enums\MariageEnum;
use Modules\Account\Enums\SexEnum;
use Modules\Account\Models\User;
use Modules\Core\Models\CompanyPtkp;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\HRMS\Enums\TaxTypeEnum;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\HRMS\Models\EmployeeTax;
use Modules\HRMS\Models\EmployeeSalary;

trait TaxYearlyRepository
{
    private $days = 20;
    private $defaultTemplate = 1;

    /**
     * Get PTKP from curent user
     * Memperbaiki error input "female" ke smallint
     */
    public function getPtkp(User $user)
    {
        $sexMeta = $user->getMeta('profile_sex', 'male');
        $sex = ($sexMeta === 'female' || $sexMeta == 2) ? 2 : 1;
        $mariageMeta = $user->getMeta('profile_mariage', 'single');
        $mariage = is_numeric($mariageMeta) ? (int)$mariageMeta : match($mariageMeta) {
            'married' => 1,
            'single'  => 2,
            default   => 2
        };

        $child = (int) $user->getMeta('profile_child', 0);
        $child = $child > 3 ? 3 : $child;

        if ($sex == 2) {
            $ptkp = CompanyPtkp::where('sex', 2)
                ->where('mariage', 2)
                ->where('child', 0)
                ->first();
        } else {
            $ptkp = CompanyPtkp::where('sex', $sex)
                ->where('mariage', $mariage)
                ->where('child', $child)
                ->first();
        }

        return [
            'value' => $ptkp->value ?? 54000000,
            'category' => $ptkp->category ?? 'A',
        ];
    }

    /**
     * Mengambil komponen gaji tahunan
     */
    public function getYearComponentValue(Employee $employee, $start_at, $end_at, $months)
    {
        $defaults = CompanySalarySlipComponent::with('category.slip')->get();
        // Ambil setting komponen pph
        $pph_components = setting('cmp_pph_components');
        $components = is_array($pph_components) ? collect($pph_components) : collect(json_decode($pph_components, true));

        $paidSalaries = EmployeeSalary::where('empl_id', $employee->id)
            ->whereBetween('start_at', [
                $start_at->format('Y-m-d'),
                $end_at->format('Y-m-d')
            ])->get();

        $data = [];

        foreach ($components as $key => $component) {
            $default = $defaults->firstWhere('id', $component['component_id']);

            if (!$default) continue;

            $totalAmountInYear = 0;

            foreach ($paidSalaries as $salary) {

                $details = is_array($salary->components)
                    ? $salary->components
                    : json_decode($salary->components, true);

                if (!$details) continue;

                foreach ($details as $slip) {

                    foreach ($slip['ctgs'] ?? [] as $ctg) {

                        foreach ($ctg['i'] ?? [] as $item) {

                            if ((int) ($item['component_id'] ?? 0) === (int) $default->id) {
                                $totalAmountInYear += (float) ($item['amount'] ?? 0);
                            }
                        }
                    }
                }
            }

            $data[$key] = [
                'component_id' => $default->id,
                'name'         => $default->name,
                'ctg_az'       => $default->category->az ?? null,
                'real_salary'  => $totalAmountInYear,
            ];
        }

        return $data;
    }

    // Fungsi store tetap menggunakan transaksi DB
    public function storeTaxYear($data, $toRecap)
    {
        return DB::transaction(function () use ($data, $toRecap) {
            $pph = new EmployeeTax($data);
            $pph->save();

            if ($toRecap) {
                EmployeeDataRecapitulation::create([
                    'empl_id'  => $pph->empl_id,
                    'type'     => DataRecapitulationTypeEnum::PPH21,
                    'start_at' => $pph->start_at->format('Y-m-d'),
                    'end_at'   => $pph->end_at->format('Y-m-d'),
                    'result'   => [
                        'id'       => $pph->id,
                        'pph'      => $pph->meta['pphtotal'] ?? 0,
                        'pph_cmp'  => $pph->meta['pph_company'] ?? 0,
                        'pph_empl' => $pph->meta['pph_employee'] ?? 0,
                    ]
                ]);
            }
            return $pph;
        });
    }
}
