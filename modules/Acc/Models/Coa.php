<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table = 'acc_coa';
    protected $fillable = ['code', 'name', 'category', 'normal_balance'];

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'coa_id');
    }

    public function beginningBalances()
    {
        return $this->hasMany(BeginningBalance::class, 'coa_id');
    }
}
