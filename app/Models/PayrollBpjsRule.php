<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBpjsRule extends Model
{
    protected $fillable = [
        'code',
        'name',
        'formula',
        'config',
        'effective_start',
        'effective_end',
        'is_active'
    ];

    protected $casts = [
        'config' => 'array',
        'effective_start' => 'date',
        'effective_end' => 'date',
        'is_active' => 'boolean'
    ];

    public function brackets()
    {
        return $this->hasMany(PayrollBpjsBracket::class, 'payroll_bpjs_rule_id');
    }

    public function getRateByClass(int $class): float
    {
        $bracket = $this->brackets()
            ->where('class', $class)
            ->first();

        return $bracket->rate ?? 0;
    }
}
