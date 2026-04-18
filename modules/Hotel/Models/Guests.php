<?php

namespace modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Hotel\Models\Booking;

class Guests extends Model
{
    protected $table = 'hotel_guests';

    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'id_card_number'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    // Helper untuk mendapatkan nama lengkap
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
