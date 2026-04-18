<?php

namespace modules\Hotel\Models;

use modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
