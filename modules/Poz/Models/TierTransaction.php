<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Userstamps\Userstamps;
use Modules\Account\Models\User;
use Modules\Poz\Models\Tier;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class TierTransaction extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes, Userstamps;

    public $table = "tiers_transaction";

    protected $fillable = [
        'name',
        'ref_tier_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'rate' => 'float',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_tier_transactions', 'transaction_tier_id', 'outlet_id');
    }

    public function tiers(){
        return $this->belongsTo(Tier::class, 'ref_tier_id');
    }
}
