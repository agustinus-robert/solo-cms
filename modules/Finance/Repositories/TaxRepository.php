<?php

namespace Modules\Finance\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Account\Models\User;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeePosition;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;
use Modules\HRMS\Enums\TaxTypeEnum;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\HRMS\Models\EmployeeTax;

trait TaxRepository
{
    public function getFinanceManager(): ?User
    {
        $ep = EmployeePosition::active()
            ->whereHas(
                'position',
                fn($position) =>
                $position->whereIn('kd', ['finances-mgr'])
            )
            ->first();

        return $ep?->employee?->user;
    }

    public function getComponentValue(Employee $employee, $start_at, $end_at)
    {
        $defaults = CompanySalarySlipComponent::with('category.slip')->get();
        $components = collect(setting('cmp_pph_ter_components'));

        $data = [];
        foreach ($components as $key => $component) {
            $default = $defaults->firstWhere('id', $component['component_id']);
            $items = $employee->activeTemplate?->items;

            if ($items && $default) {
                $amount = 0;
                $multiplier = ($default->meta?->as_multiplier ?? false)
                    ? ($items->firstWhere('component_id', $default->id)?->amount ?? 0)
                    : 1;

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
                                    $column = parseJsonColumn($x->action_column);

                                    $value = $query->{$x->action}($column);

                                    $amount += match ($x->after) {
                                        'multiply_by_self_overdays' => $value * $employee->getOverdaysSalary(),
                                        default => $value
                                    };
                                }
                            } else {
                                $column = parseJsonColumn($x->action_column);
                                $amount += $query->{$x->action}($column);
                            }
                        }
                        break;

                    default:
                        $amount = $items->firstWhere('component_id', $default->id)?->amount ?? 0;
                        break;
                }

                $data[$key] = $component;
                $data[$key]['salary_multiplier'] = $multiplier;
                $data[$key]['salary_amount'] = $amount;
                $data[$key]['real_salary'] = $amount * $multiplier;
            }
        }

        // Unset reference to avoid unintended side effects
        unset($component);

        return $data;
    }

    public function processEmployeeTerTax($employee, $start_at, $end_at)
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
            'type'      => (TaxTypeEnum::TER)->value,
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

    public function storeTaxTer($data, $toRecap)
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
