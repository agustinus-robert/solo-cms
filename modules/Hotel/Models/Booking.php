<?php

namespace modules\Hotel\Models;

use modules\Hotel\Enums\BookingStatus;
use modules\Hotel\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $table = 'hotel_bookings';

    protected $fillable = [
        'guest_id', 'room_id', 'check_in_plan', 'check_out_plan',
        'actual_check_in', 'actual_check_out', 'total_price',
        'status', 'payment_status', 'notes'
    ];

    protected $casts = [
        'check_in_plan' => 'datetime',
        'check_out_plan' => 'datetime',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'status' => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function additionalServices(): HasMany
    {
        return $this->hasMany(AdditionalService::class, 'booking_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(BookingSource::class, 'source_id');
    }
}
