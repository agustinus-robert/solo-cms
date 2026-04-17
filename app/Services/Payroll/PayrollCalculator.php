<?php

namespace App\Services\Payroll;

use App\Models\PayrollRule;
use Modules\Core\Models\CompanyPtkp;
use Exception;

class PayrollCalculator
{
    public function calculate($code, $context, $startAt)
    {
        $rule = PayrollRule::where('code', $code)
            ->where('effective_start', '<=', $startAt)
            ->where(function($q) use ($startAt) {
                $q->where('effective_end', '>=', $startAt)->orWhereNull('effective_end');
            })
            ->first();

        if (!$rule) throw new Exception("Aturan pajak dengan kode $code tidak ditemukan!");

        $serviceClass = $rule->formula;
        if (!class_exists($serviceClass)) throw new Exception("Service $serviceClass tidak ada!");

        $taxService = app($serviceClass);
        $bruto = $context['gaji'] ?? $context['penghasilan'] ?? 0;

        return $taxService->calculate($bruto, $context, $rule->brackets);
    }

    public function calculateTax($employee, $bruto, $startAt, $endAt)
    {
        $ptkp = CompanyPtkp::where('sex', $employee->sex)
            ->where('mariage', $employee->mariage)
            ->where('child', $employee->child)
            ->where('effective_start', '<=', $startAt)
            ->where(function($q) use ($startAt) {
                $q->where('effective_end', '>=', $startAt)->orWhereNull('effective_end');
            })->first();

        if (!$ptkp) throw new Exception("Data PTKP tidak ditemukan!");
        $rule = PayrollRule::where('effective_start', '<=', $startAt)
            ->where(function($q) use ($startAt) {
                $q->where('effective_end', '>=', $startAt)->orWhereNull('effective_end');
            })
            ->first();

        if (!$rule) throw new Exception("Tidak ada aturan pajak aktif untuk periode ini!");

        $serviceClass = $rule->formula;
        if (!class_exists($serviceClass)) throw new Exception("Service $serviceClass tidak ada!");

        $taxService = app($serviceClass);
        $config = [
            'ptkp_value'    => $ptkp->value,
            'ptkp_category' => $ptkp->category,
            'start_at'      => $startAt,
            'end_at'        => $endAt,
            'is_daily'      => $employee->is_daily ?? false,
            'total_bruto_setahun' => $employee->total_bruto_setahun ?? 0,
            'total_pph_jan_nov'   => $employee->total_pph_jan_nov ?? 0,
        ];

        return $taxService->calculate($bruto, $config, $rule->brackets);
    }
}
