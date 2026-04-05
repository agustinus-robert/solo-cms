<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBpjsBracket extends Model
{
    protected $fillable = [
        'payroll_bpjs_rule_id',
        'min',
        'max',
        'rate'
    ];

    public function rule()
    {
        return $this->belongsTo(PayrollBpjsRule::class, 'payroll_bpjs_rule_id');
    }
}
