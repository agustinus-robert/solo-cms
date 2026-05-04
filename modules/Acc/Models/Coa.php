<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Acc\Enums\AccountCategory;
use Modules\Acc\Enums\NormalBalance;

class Coa extends Model
{
    protected $table = 'acc_coas';
    protected $fillable = ['code', 'name', 'category', 'normal_balance'];

    protected $casts = [
        'category' => AccountCategory::class,
        'normal_balance' => NormalBalance::class,
    ];

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'coa_id');
    }

    public function beginningBalances()
    {
        return $this->hasMany(BeginningBalance::class, 'coa_id');
    }
}
