<?php

namespace App\Services\Payroll\Tax;

use Carbon\Carbon;
use App\Services\Payroll\TerRateService;
use Modules\HRMS\Models\Employee;
use Modules\Finance\Repositories\TaxYearlyRepository;
use Modules\HRMS\Models\EmployeeDataRecapitulation;
use Modules\HRMS\Models\EmployeeTax;
use Modules\HRMS\Enums\DataRecapitulationTypeEnum;

use Illuminate\Support\Facades\DB;

class Tax2024Service implements PayrollTaxInterface
{
    use TaxYearlyRepository; // Gunakan repository yang sama agar fungsi getYearComponentValue dll tersedia

    protected $terRateService;

    public function __construct()
    {
        $this->terRateService = new TerRateService();
    }

    /**
     * Pintu utama kalkulasi PPh 21
     */
    public function calculate(float $bruto, array $config, $brackets): float
    {
        if (isset($config['is_daily']) && $config['is_daily'] === true) {
            $rate = ($bruto <= 450000) ? 0 : 0.5;
            return floor($bruto * ($rate / 100));
        }

        $date = Carbon::parse($config['start_at']);
        if ($date->month != 12) {
            return $this->calculateTer($bruto, $config);
        }

        return $this->calculateYearly($bruto, $config);
    }

    /**
     * FUNGSI PINDAHAN DARI TAXYEARLY
     * Menyiapkan data bruto, ptkp, dan bulan kerja karyawan
     */
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
     * FUNGSI TAXYEARLY
     */
    private function calculateEffectiveMonths(Employee $employee, int $year, Carbon $endOfYear): int
    {
        $joinDate = $employee->joined_at;

        if (!$joinDate || $joinDate->year < $year) {
            return 12;
        }

        $diff = $joinDate->diffInMonths($endOfYear) + 1;
        return $diff > 12 ? 12 : $diff;
    }

    private function calculateTer(float $bruto, array $config): float
    {
        $category = $config['ptkp']['category'] ?? ($config['ptkp_category'] ?? 'A');
        $rates = $this->terRateService->getRatesByCategory($category);

        $rate = 0;
        foreach ($rates as $r) {
            if ($bruto >= $r['lower'] && ($r['upper'] === null || $bruto < $r['upper'])) {
                $rate = $r['percentage'];
                break;
            }
        }
        return floor($bruto * ($rate / 100));
    }

    private function calculateYearly(float $bruto, array $config): float
    {
        $brutoSetahun = $config['total_bruto_setahun'] ?? ($bruto * 12);
        $pphTerbayar  = $config['total_pph_jan_nov'] ?? 0;

        $pajakSetahun = $this->calculateAnnualProgresive($brutoSetahun, $config);

        return max(0, $pajakSetahun - $pphTerbayar);
    }

    private function calculateAnnualProgresive(float $brutoSetahun, array $config): float
    {
        $biayaJabatan = min($brutoSetahun * 0.05, 6000000);
        $totalReductions = collect($config['reductions'] ?? [])->sum('real_salary');
        $nettoSetahun = $brutoSetahun - $biayaJabatan - $totalReductions;

        $ptkpValue = $config['ptkp']['value'] ?? 54000000;

        $pkp = max(0, floor(($nettoSetahun - $ptkpValue) / 1000) * 1000);

        $progressiveBrackets = $this->terRateService->getProgressiveBrackets();
        $taxTotal = 0;
        $prevMax = 0;

        $isNpwp = $config['is_npwp'] ?? true;

        foreach ($progressiveBrackets as $b) {
            if ($pkp <= 0) break;

            $range = $b['max'] ? ($b['max'] - $prevMax) : $pkp;

            if ($pkp > $range && $b['max']) {
                $taxTotal += $range * ($b['rate'] / 100);
                $pkp -= $range;
                $prevMax = $b['max'];
            } else {
                $taxTotal += $pkp * ($b['rate'] / 100);
                $pkp = 0;
                break;
            }
        }

        if (!$isNpwp) {
            $taxTotal = $taxTotal * 1.2;
        }

        return (float) $taxTotal;
    }

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
                        'pph'      => $pph->meta->pphtotal ?? 0,
                        'pph_cmp'  => $pph->meta->pph_company ?? 0,
                        'pph_empl' => $pph->meta->pph_employee ?? 0,
                    ]
                ]);
            }
            return $pph;
        });
    }
}
