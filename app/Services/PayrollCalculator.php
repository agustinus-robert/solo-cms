<?php

namespace App\Services;

use App\Models\PayrollRule;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Carbon\Carbon;

class PayrollCalculator
{
    protected ExpressionLanguage $expression;

    public function __construct()
    {
        $this->expression = new ExpressionLanguage();
    }

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
        $variables = array_merge($context, $rule->config ?? []);
        $ptkpResolver = app(PtkpResolver::class);

        $variables['ptkp'] = $ptkpResolver->resolve($context, $date);
        $variables['rate_lookup'] = $this->lookupRate(
            $rule,
            $context['gaji'] ?? $context['penghasilan'] ?? 0
        );

        $variables['progressive'] = function ($pkp) use ($rule) {
            return $this->calculateProgressive($rule, $pkp);
        };

        return $this->expression->evaluate($rule->formula, $variables);
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
