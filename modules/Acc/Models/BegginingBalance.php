<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class BeginningBalance extends Model
{
    protected $table = 'acc_beginning_balances';
    protected $fillable = ['period_id', 'coa_id', 'amount'];

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
