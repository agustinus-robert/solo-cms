<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRule extends Model
{
    protected $table = "payroll_rules";

    protected $fillable = [
        'code',
        'name',
        'formula',
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
}
