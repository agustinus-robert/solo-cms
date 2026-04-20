<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use  \Modules\Hotel\Enums\InventoryTypeEnum;

class Inventory extends Model
{
    protected $table = 'hotel_ref_inventories';
    protected $fillable = ['name', 'type', 'total_stock', 'unit', 'min_stock', 'description'];

    protected $casts = [
        'type' => InventoryTypeEnum::class,
    ];

    public function getTypeBadgeAttribute()
    {
        return $this->type === 'asset' ? 'bg-soft-primary text-primary' : 'bg-soft-info text-info';
    }
}
