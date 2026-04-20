<?php

namespace modules\Hotel\Models;

use modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Models\Booking;
use Modules\Hotel\Models\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $table = 'hotel_rooms';

    protected $fillable = ['room_type_id', 'room_number', 'floor', 'status'];

    protected $casts = [
        'status' => RoomStatusEnum::class,
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'hotel_room_inventories', 'room_id', 'inventory_id')
            ->withPivot('quantity', 'note')
            ->withTimestamps();
    }

    public function getStockInGudangAttribute()
    {
        return $this->total_stock - $this->rooms()->sum('hotel_room_inventories.quantity');
    }
}
