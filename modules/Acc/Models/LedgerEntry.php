<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'acc_ledger_entries';
    protected $fillable = [
        'ledger_id',
        'coa_id',
        'department_tag',
        'debit',
        'credit'
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('department_tag', $module);
    }
}
