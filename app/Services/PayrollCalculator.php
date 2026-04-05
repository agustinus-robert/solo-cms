<?php

namespace App\Services;

use App\Models\PayrollRule;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Carbon\Carbon;
use App\Services\PtkpResolver;

class PayrollCalculator
{
    protected ExpressionLanguage $expression;

    public function __construct()
    {
        $this->expression = new ExpressionLanguage();
    }

    /**
     * Hitung payroll sesuai rule (TER, PPH21, dll)
     *
     * @param string $code
     * @param array  $context
     * @param string|Carbon $date
     */
    public function calculate(string $code, array $context, $date)
    {
        $date = Carbon::parse($date);

        $rule = PayrollRule::with('brackets')
            ->where('code', $code)
            ->where('is_active', true)
            ->where('effective_start', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_end')
                  ->orWhere('effective_end', '>=', $date);
            })
            ->orderByDesc('effective_start')
            ->first();

        if (!$rule) {
            throw new \Exception("Rule {$code} tidak ditemukan");
        }

        return $this->evaluate($rule, $context, $date);
    }

    protected function evaluate($rule, $context, $date)
    {
        $ptkpResolver = app(PtkpResolver::class);

        $employeeType = $context['employee_type'] ?? 'monthly';

        if ($employeeType === 'daily') {
            $jamKerja     = $context['jam_kerja_per_hari'] ?? 0;
            $upahPerJam   = $context['upah_per_jam'] ?? 0;
            $hariKerja    = $context['hari_kerja'] ?? 0;

            $gajiHarian        = $jamKerja * $upahPerJam;
            $gajiBulananEquiv  = $gajiHarian * $hariKerja;
            $grossAnnum        = $gajiBulananEquiv * 12 / 30;
        } else {
            $grossAnnum = ($context['gaji'] ?? 0) * 12;
        }

        $ptkp = $ptkpResolver->resolve($context, $date);
        $pkp  = max(0, $grossAnnum - $ptkp);

        $variables = array_merge($context, $rule->config ?? []);
        $variables['gaji']       = $grossAnnum / 12;
        $variables['penghasilan'] = $grossAnnum;
        $variables['ptkp']       = $ptkp;
        $variables['pkp']        = $pkp;

        $variables['rate_lookup'] = $this->lookupRate(
            $rule,
            $context['gaji'] ?? $context['penghasilan'] ?? $grossAnnum
        );

        $variables['progressive'] = function ($pkpVal) use ($rule) {
            return $this->calculateProgressive($rule, $pkpVal);
        };

        $result = $this->expression->evaluate($rule->formula, $variables);

        if ($rule->code === 'PPH21') {
            return $result / 12;
        }

        return $result;
    }

    protected function lookupRate($rule, $value)
    {
        foreach ($rule->brackets as $b) {
            if (
                ($b->min === null || $value >= $b->min) &&
                ($b->max === null || $value <= $b->max)
            ) {
                return $b->rate;
            }
        }

        return 0;
    }

    protected function calculateProgressive($rule, $pkp)
    {
        $tax = 0;

        foreach ($rule->brackets as $b) {
            $min = $b->min ?? 0;
            $max = $b->max ?? $pkp;

            if ($pkp <= $min) continue;

            $taxable = min($pkp, $max) - $min;

            if ($taxable > 0) {
                $tax += $taxable * $b->rate;
            }
        }

        return $tax;
    }
}
