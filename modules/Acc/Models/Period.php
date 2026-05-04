<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'acc_periods';
    protected $fillable = ['name', 'start_date', 'end_date', 'is_closed'];

    public function beginningBalances()
    {
        return $this->hasMany(BeginningBalance::class, 'period_id');
    }
}
