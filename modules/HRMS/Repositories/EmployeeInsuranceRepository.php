<?php

namespace Modules\HRMS\Repositories;

use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\CompanyInsurance;
use Modules\Core\Models\CompanyInsurancePrice;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeInsurance;

trait EmployeeInsuranceRepository
{
    protected Collection $batchErrors;

    /**
     * Store newly created resource.
     */
    public function storeEmployeeInsurance(Employee $employee, array $data)
    {
        if ($insurance = $employee->insurances()->save(new EmployeeInsurance($data))) {
            Auth::user()->log('menambahkan asuransi karyawan ' . $insurance->employee->user->name . ' <strong>[ID: ' . $insurance->id . ']</strong>', EmployeeInsurance::class, $insurance->id);
            return $insurance;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyInsurance(EmployeeInsurance $insurance)
    {
        if (!$insurance->trashed() && $insurance->delete()) {
            Auth::user()->log('menghapus asuransi karyawan ' . $insurance->employee->user->name . ' <strong>[ID: ' . $insurance->id . ']</strong>', EmployeeInsurance::class, $insurance->id);
            return $insurance;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreInsurance(EmployeeInsurance $insurance)
    {
        if ($insurance->trashed() && $insurance->restore()) {
            Auth::user()->log('memulihkan asuransi karyawan ' . $insurance->employee->user->name . ' <strong>[ID: ' . $insurance->id . ']</strong>', EmployeeInsurance::class, $insurance->id);
            return $insurance;
        }
        return false;
    }

    public function getComponentValue(Employee $employee, $template, $start_at, $end_at)
    {
        static $defaults = null;

        if ($defaults === null) {
            $defaults = CompanySalarySlipComponent::with('category.slip')->get();
        }

        $components = collect(setting($template));

        $data = [];
        foreach ($components as $key => $component) {
            $default = $defaults->firstWhere('id', $component['component_id']);
            // $items = $employee->lastSalaryTemplate?->items;
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

    public function getInsuranceSalary(Employee $employee, $start_at, $end_at)
    {
        $insurances = CompanyInsurance::with('prices.insurance')->get();

        $conditions = $insurances
            ->mapWithKeys(function ($i) {
                $meta = json_decode($i->meta, true);
                return [$i->kd => $meta['conditions'] ?? []];
            });

        $types = collect($conditions)
            ->map(function ($items) {
                return collect($items)
                    ->whereIn('key', ['group', 'services'])
                    ->pluck('values')
                    ->flatten()
                    ->values();
            })
            ->filter(fn($v) => $v->isNotEmpty());

        $salaries = [];

        foreach ($types as $key => $type) {
            $template = $key;
            $realComponentSalary = $this->getComponentValue($employee, $template, $start_at, $end_at);
            $realSalary = collect($realComponentSalary)->sum('real_salary');

            // Default
            $finalSalary = 0;

            foreach ($type as $key => $subType) {
                // BPJS Ketenagakerjaan standar
                if ($template == 'bpjs-ketenagakerjaan') {
                    $finalSalary = $realSalary;
                }

                // BPJS Ketenagakerjaan pensiun cek max salary pensiun dulu
                if ($template == 'bpjs-ketenagakerjaan' && $subType == 'Jaminan Pensiun') {
                    $max = bpjs_tk_pensiun_max_salary();
                    $finalSalary = $realSalary > $max ? $max : $realSalary;
                }

                // BPJS Kesehatan → pakai min max salary
                if ($template == 'bpjs-kesehatan') {

                    $min = bpjs_min_salary();
                    $max = bpjs_max_salary();

                    // Tentukan kelas berdasarkan salary
                    $salaryClass = $realSalary >= 4000000 ? 'Kelas 1' : 'Kelas 2';

                    foreach ($type as $key => $subType) {

                        // Hanya Kelas 1 atau 2 yang aktif, lainnya final_salary = 0
                        if ($subType === $salaryClass) {
                            $finalSalary = min(max($realSalary, $min), $max);
                        } else {
                            $finalSalary = 0; // atau null jika ingin kosong
                        }

                        $salaries[$template][$subType] = [
                            'real_salary'  => $realSalary,
                            'final_salary' => $finalSalary,
                        ];
                    }

                    continue;
                }

                $salaries[$template][$subType] = [
                    'real_salary'  => $realSalary,
                    'final_salary' => $finalSalary,
                    // 'components'   => collect($realComponentSalary)
                    //     ->map(fn($i) => [
                    //         'label' => $i['name'],
                    //         'value' => $i['real_salary']
                    //     ]),
                ];
            }
        }

        return $salaries;
    }

    public function getPriceInsurance($insuranceHealthId): array
    {
        return CompanyInsurancePrice::where('insurance_id', $insuranceHealthId)
            ->pluck('id')
            ->toArray();
    }

    public function getBatchErrors(): Collection
    {
        return $this->batchErrors ?? collect();
    }

    public function doResetInsurance(Employee $employee, $prices): bool
    {
        try {
            DB::transaction(function () use ($employee, $prices) {
                $employee->load('insurances');

                $employee->insurances()
                    ->whereIn('price_id', $prices)
                    ->delete();
            });

            Log::info(
                'berhasil reset asuransi karyawan ' . $employee->user->name . ' [ID: ' . $employee->id . ']',
                ['price_ids' => $prices]
            );

            return true;
        } catch (\Throwable $th) {
            Log::error(
                'terjadi kesalahan saat reset asuransi karyawan ' . $employee->user->name . ' <strong>[ID: ' . $employee->id . ']</strong>',
                ['data' => $th->getMessage()]
            );
            return false;
        }
    }

    public function batchResetInsurance($employees, $prices)
    {
        $this->batchErrors = collect();

        foreach ($employees as $key => $employee) {
            try {
                $this->doResetInsurance($employee, $prices);
            } catch (\Throwable $th) {
                $this->batchErrors->push([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->user->name,
                    'error' => $th->getMessage(),
                ]);

                Log::warning(
                    'skip reset asuransi karyawan ' . $employee->user->name . ' [ID: ' . $employee->id . ']',
                    ['error' => $th->getMessage()]
                );
            }
        }

        return true;
    }
}
