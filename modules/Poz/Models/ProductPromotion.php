<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Userstamps\Userstamps;
use Modules\Poz\Enums\Prizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPromotion extends Model
{
    use HasFactory, HasAuditLog, Restorable, SoftDeletes, Userstamps;

    public $table = "product_promotions";

    protected $fillable = [
        'name',
        'type',
        'config',
        'start_date',
        'end_date',
        'location',
        'image_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'type' => Prizer::class,
        'config' => 'array', // Ini sangat penting agar $promotion->config['min_qty'] bisa dibaca langsung
        'start_date' => 'date',
        'end_date' => 'date',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_product_promotions', 'transaction_product_promotion_id', 'outlet_id');
    }
}
