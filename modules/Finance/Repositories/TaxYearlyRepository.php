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
     * @param: user
     *
     * @return int
     */
   public function getPtkp(User $user)
    {
        $sex = $user->getMeta('profile_sex', SexEnum::MALE);
        $mariage = $user->getMeta('profile_mariage', MariageEnum::SINGLE);
        $child = (int) $user->getMeta('profile_child', 0);
        $child = $child > 3 ? 3 : $child;

        if ($sex == SexEnum::FEMALE) {
            $ptkp = CompanyPtkp::whereSex(SexEnum::FEMALE)
                ->whereMariage(MariageEnum::SINGLE)
                ->whereChild(0)
                ->first();
        } else {
            $ptkp = CompanyPtkp::whereSex($sex)
                ->whereMariage($mariage)
                ->whereChild($child)
                ->first();
        }

        return $ptkp->value ?? 54000000;
    }

    /**
     * Get employee salary component
     * @param: employee
     * @param: start_at
     * @param: end_at
     * @param: months
     *
     */
    public function getYearComponentValue(Employee $employee, $start_at, $end_at, $months)
    {
        $defaults = CompanySalarySlipComponent::with('category.slip')->get();
        $components = collect(setting('cmp_pph_components'));

        $paidSalaries = EmployeeSalary::where('empl_id', $employee->id)
            ->whereBetween('start_at', [$start_at->format('Y-m-d'), $end_at->format('Y-m-d')])
            ->get();

        $data = [];
        foreach ($components as $key => $component) {
            $default = $defaults->firstWhere('id', $component['component_id']);

            if ($default) {
                $totalAmountInYear = 0;

                foreach ($paidSalaries as $salary) {
                    $details = is_array($salary->details) ? $salary->details : json_decode($salary->details, true);

                    if (!$details) continue;

                    foreach ($details as $slip) {
                        if (!isset($slip['ctgs'])) continue;

                        foreach ($slip['ctgs'] as $ctg) {
                            if (!isset($ctg['i'])) continue;

                            foreach ($ctg['i'] as $item) {
                                if ($item['component_id'] == $default->id) {
                                    $totalAmountInYear += (float) ($item['amount'] ?? 0);
                                }
                            }
                        }
                    }
                }

                $data[$key] = array_merge($component, [
                    'salary_multiplier' => 1,
                    'salary_amount'     => $totalAmountInYear,
                    'real_salary'       => $totalAmountInYear,
                    'real_multiplier'   => 1,
                ]);
            }
        }

        return $data;
    }

    public function processEmployeeYearTax($employee, $start_at, $end_at)
    {
        Log::info("Start calculating tax ter for employee {$employee->user->name}");

        $terData = getTERCategory(
            $employee->user->getMeta('profile_mariage'),
            $employee->user->getMeta('profile_child'),
            $employee->user->getMeta('profile_sex')
        );

        $data = $this->getComponentValue($employee, $start_at, $end_at);
        $configs = setting('cmp_pph_objective_percentage');

        // Transform income
        $income = collect($data)->sortby(['slip_az', 'ctg_az', 'az'])->groupBy(['slip_az'])->map(function ($salary) {
            $firstData = $salary->first();
            return [
                'az' => $firstData['slip_az'],
                'slip' => $firstData['slip_name'],
                'ctgs' => collect($salary)->whereIn('ctg_az', [1, 2])->groupBy(['slip_az'])->map(function ($c) {
                    return [
                        'az' => $c->first()['ctg_az'],
                        'ctg' => $c->first()['ctg_name'],
                        'item' => $c->map(function ($i) {
                            return [
                                'component_id' => $i['component_id'],
                                'name' => $i['name'],
                                'monthly' => $i['real_salary'],
                            ];
                        }),
                        'month' => $c->sum('real_salary'),
                    ];
                })->values(),
                'totalmonth' => $salary->sum('real_salary'),
            ];
        });

        $reductions = collect($data)->where('ctg_az', 3)->count() > 0 ? collect($data)->where('ctg_az', 3) : null;
        $totalMonth = $income->sum('totalmonth') ?? 0;
        $status = $terData['status'];
        $ter = $terData['ter'];

        // Langsung cari rate
        $rate = collect($terData['rate'])->first(function ($r) use ($totalMonth) {
            $lower = (float) $r['lower'];
            $upper = $r['upper'] ? (float) $r['upper'] : null;
            return is_null($upper)
                ? $totalMonth >= $lower
                : $totalMonth >= $lower && $totalMonth < $upper;
        });

        // Ambil hanya persentase dalam bentuk float
        $ratePercentage = isset($rate['percentage'])
            ? (float) $rate['percentage']
            : 0;

        // Jika mau dalam bentuk desimal untuk perhitungan (misal 2.00 -> 0.02)
        $rateDecimal = $ratePercentage / 100;

        // PPh TER
        $pphTer = floor($rateDecimal * $totalMonth);

        // Transform data yang akan disimpan ke database
        $dataTax = [
            'empl_id'   => $employee->id,
            'start_at'  => $start_at,
            'end_at'    => $end_at,
            'type'      => (TaxTypeEnum::YEARLY)->value,
            'meta'      => [
                'income' => $income,
                'reduction' => $reductions,
                'charge' => null,
                'netto' => null,
                'ptkp' => null,
                'pkp' => $totalMonth,
                'pph1' => null,
                'pph2' => null,
                'pph3' => null,
                'pph4' => null,
                'pph5' => null,
                'category' => $status,
                'ter_category' => $ter,
                'rate' => $ratePercentage,
                'pph_employee' => floor($configs['employee']['rate'] / 100 * $pphTer),
                'pph_company' => floor($configs['company']['rate'] / 100 * $pphTer),
                'pphtotal' => $pphTer,
            ]
        ];

        $this->storeTaxTer($dataTax, $recap = true);
    }

    public function storeTaxYear($data, $toRecap)
    {
        return DB::transaction(function () use ($data, $toRecap) {

            $pph = new EmployeeTax($data);

            $pph->save();

            if ($toRecap) {
                $recap = new EmployeeDataRecapitulation([
                    'empl_id' => $pph->empl_id,
                    'type' => DataRecapitulationTypeEnum::PPH21,
                    'start_at' => $pph->start_at->format('Y-m-d'),
                    'end_at' => $pph->end_at->format('Y-m-d'),
                    'result' => [
                        'id' => $pph->id,
                        'pph' => $pph->meta?->pphtotal ?? null,
                        'pph_cmp' => $pph->meta?->pph_company ?? null,
                        'pph_empl' => $pph->meta?->pph_employee ?? null,
                    ]
                ]);

                $recap->save();
            }

            Log::info("Successfully stored Tax TER for employee {$pph->employee->user->name}");

            return $pph;
        });
    }
}
