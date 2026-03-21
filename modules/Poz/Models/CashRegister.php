<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashRegister extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    public $table = "cash_registers";

    protected $fillable = [
        'casier_id',
        'money',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'money'     => 'float',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function logCash(){
        return $this->hasMany(CashHistoryRegister::class, 'cash_register_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_cash_registers', 'cash_register_id', 'outlet_id');
    }
}
