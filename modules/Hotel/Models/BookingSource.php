<?php

namespace modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSource extends Model
{
    protected $table = 'hotel_ref_sources';
    protected $fillable = ['name', 'commission_rate'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'source_id');
    }
}
