<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Hotel\Enums\InventoryTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Hotel\Models\InventoryAdjustment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use modules\Hotel\Models\Room;

class Inventory extends Model
{
    protected $table = 'hotel_ref_inventories';
    protected $fillable = ['name', 'type', 'total_stock', 'unit', 'min_stock', 'description'];

    protected $casts = [
        'type' => InventoryTypeEnum::class,
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'hotel_room_inventories', 'inventory_id', 'room_id')
                    ->withPivot('quantity', 'note')
                    ->withTimestamps();
    }

    /**
     * Helper Badge - Perbaikan Logika Enum
     */
    public function getTypeBadgeAttribute()
    {
        // Bandingkan dengan Case Enum, bukan string manual
        return $this->type === InventoryTypeEnum::ASSET
            ? 'bg-soft-primary text-primary'
            : 'bg-soft-info text-info';
    }


    public function adjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'inventory_id');
    }

    public function getCurrentStockAttribute(): int
    {
        return $this->adjustments()->where('status', 'plus')->sum('quantity')
             - $this->adjustments()->where('status', 'minus')->sum('quantity');
    }
}
