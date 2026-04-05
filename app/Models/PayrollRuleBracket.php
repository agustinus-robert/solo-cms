<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRuleBracket extends Model
{
    protected $fillable = [
        'payroll_rule_id',
        'min',
        'max',
        'rate'
    ];

    public function rule()
    {
        return $this->belongsTo(PayrollRule::class);
    }
}
