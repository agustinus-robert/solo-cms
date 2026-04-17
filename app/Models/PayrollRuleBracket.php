<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRuleBracket extends Model
{
    protected $table = "payroll_rule_brackets";

    protected $fillable = [
        'payroll_rule_id',
        'min',
        'max',
        'rate',
        'class'
    ];
}
