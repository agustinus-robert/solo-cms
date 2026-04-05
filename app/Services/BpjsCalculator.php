<?php

namespace App\Services;

use App\Models\PayrollBpjsRule;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Carbon\Carbon;

class BpjsCalculator
{
    protected ExpressionLanguage $expression;

    public function __construct()
    {
        $this->expression = new ExpressionLanguage();
    }

    /**
     * Hitung BPJS berdasarkan kelas peserta
     *
     * @param string $code
     * @param array $context ['gaji'=>..., 'class'=>1|2|3]
     * @param string|Carbon $date
     */
    public function calculate(string $code, array $context, $date)
    {
        $date = Carbon::parse($date);

        $rule = PayrollBpjsRule::with('brackets')
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
            throw new \Exception("Rule BPJS {$code} tidak ditemukan");
        }

        $variables = array_merge($context, $rule->config ?? []);

        $class = $context['class'] ?? 1;
        $variables['rate_lookup'] = $rule->getRateByClass($class);

        return $this->expression->evaluate($rule->formula, $variables);
    }

    // $bpjsCalc = app(\App\Services\BpjsCalculator::class);

    // Kelas 1
    // $bpjs1 = $bpjsCalc->calculate('BPJS_KES', [
    //     'gaji' => 12000000,
    //     'class' => 1
    // ], now());

    // Kelas 2
    // $bpjs2 = $bpjsCalc->calculate('BPJS_KES', [
    //     'gaji' => 12000000,
    //     'class' => 2
    // ], now());

    // Kelas 3
    // $bpjs3 = $bpjsCalc->calculate('BPJS_KES', [
    //     'gaji' => 12000000,
    //     'class' => 3
    // ], now());
}
