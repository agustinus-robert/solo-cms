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
        return $user->getMeta('profile_sex') == SexEnum::FEMALE
            ? CompanyPtkp::whereSex(SexEnum::FEMALE)->whereMariage(MariageEnum::SINGLE)->whereChild(0)->first()->value
            : CompanyPtkp::whereSex($user->getMeta('profile_sex', SexEnum::MALE))
            ->whereMariage($user->getMeta('profile_mariage', MariageEnum::SINGLE))
            ->whereChild($user->getMeta('profile_child') > 3 ? 3 : $user->getMeta('profile_child', 0))->first()->value;
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
        $items = $employee->salaryTemplates()
            ->with('items.component',)
            ->where('cmp_template_id', $this->defaultTemplate)
            ->where('start_at', '>=', $start_at)
            ->where('end_at', '<=', $end_at)
            ->latest()
            ->first()?->items ?? [];

        $data = [];
        foreach ($components as $key => $component) {
            $default = $defaults->firstWhere('id', $component['component_id']);
            // $items = $employee->lastSalaryTemplate?->items;

            if ($items && $default) {
                $amount = 0;
                $is_mutiplier = $default->meta?->as_multiplier ?? false;
                $multiplier = $is_mutiplier ? ($items->firstWhere('component_id', $default->id)?->amount ?? 0) : 1;

                switch ($default->meta?->algorithm?->method ?? null) {
                    case 'MODEL':
                        foreach (($default->meta->algorithm?->models ?? []) as $model => $x) {
                            $query = new $model;
                            foreach ($x->conditions as $clauses) {
                                foreach ($clauses->p as $i => $clause) {
                                    $clauses->p[$i] = match ($clause) {
                                        '%CURRENT_EMPL_ID%' => $employee->id,
                                        '%START_AT%' => $start_at->format('Y-m-d'),
                                        '%END_AT%' => $end_at->format('Y-m-d'),
                                        '%COMPONENT_ID%' => $default->id,
                                        default => $clause
                                    };
                                }
                                $query = $query->{$clauses->f}(...$clauses->p);
                            }

                            if (isset($x->after)) {
                                foreach ($query->get() as $recap) {
                                    $amount += match ($x->after) {
                                        'multiply_by_self_overdays' => $query->{$x->action}($x->action_column) * $employee->getOverdaysSalary(),
                                        default => $query->{$x->action}($x->action_column)
                                    };
                                }
                            } else {
                                $amount += $query->{$x->action}($x->action_column);
                            }
                        }
                        break;

                    default:
                        $amount = $items->firstWhere('component_id', $default->id)?->amount ?? 0;
                        break;
                }

                $processingResult = $is_mutiplier ? $this->days : $amount;
                $processingMonth = isset($component['multiplier']) ? $months : 1;

                $data[$key] = $component;
                $data[$key]['salary_multiplier'] = $multiplier;
                $data[$key]['salary_amount'] = $processingResult;
                $data[$key]['real_salary'] = $processingResult * $multiplier;
                $data[$key]['real_multiplier'] = $processingMonth;
            }
        }

        unset($component);

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
