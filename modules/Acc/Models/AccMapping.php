<?php

namespace Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class AccMapping extends Model
{
    protected $table = 'acc_mappings';
    protected $fillable = ['module', 'transaction_type', 'coa_id'];

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
