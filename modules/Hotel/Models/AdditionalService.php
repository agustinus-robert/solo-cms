<?php

namespace modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalService extends Model
{
    protected $table = 'hotel_additional_services';

    protected $fillable = ['booking_id', 'service_name', 'price', 'quantity'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
