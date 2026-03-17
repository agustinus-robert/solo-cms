<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashHistoryRegister extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    public $table = "cash_register_logs";

    protected $fillable = [
        'cash_register_id',
        'status',
        'money',
    ];

    protected $casts = [
        'money' => 'float',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
