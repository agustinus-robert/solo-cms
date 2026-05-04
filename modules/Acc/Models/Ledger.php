<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ledger extends Model
{
    protected $table = 'acc_ledgers';
    protected $fillable = [
        'transaction_date',
        'reference_number',
        'description',
        'source_module',
        'user_id'
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'ledger_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
