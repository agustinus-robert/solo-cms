<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Enums\TaxTypeEnum;
use Modules\Finance\Repositories\TaxYearlyRepository;

class TaxYearly
{
    use TaxYearlyRepository;

    public function prepareYearlyTaxData(int $employeeId, int $year): array
    {
        $date = Carbon::createFromDate($year);
        $start_at = $date->copy()->startOfYear();
        $end_at = $date->copy()->endOfYear();

        $employee = Employee::has('contract')->findOrFail($employeeId);

        $months = $this->calculateEffectiveMonths($employee, $year, $end_at);

        $isNpwp = !empty($employee->user->getMeta('tax_number'));
        $ptkp = $this->getPtkp($employee->user);

        $componentValues = $this->getYearComponentValue($employee, $start_at, $end_at, $months);

        $data = collect($componentValues);

        return [
            'year'       => $year,
            'start_at'   => $start_at,
            'end_at'     => $end_at,
            'employee'   => $employee,
            'is_npwp'    => $isNpwp,
            'ptkp'       => $ptkp,
            'months'     => $months,
            'earnings'   => $data->filter(fn($i) => in_array($i['ctg_az'], [1, 2])),
            'reductions' => $data->filter(fn($i) => $i['ctg_az'] == 3),
        ];
    }

    /**
     * Logika hitung bulan efektif kerja dalam 1 tahun pajak
     */
    private function calculateEffectiveMonths(Employee $employee, int $year, Carbon $endOfYear): int
    {
        $joinDate = $employee->joined_at;

        if ($joinDate->year < $year) {
            return 12;
        }

        $diff = $joinDate->diffInMonths($endOfYear) + 1;
        return $diff > 12 ? 12 : $diff;
    }
}
