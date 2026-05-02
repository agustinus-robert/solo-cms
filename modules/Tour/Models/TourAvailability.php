<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;

class TourAvailability extends Model
{
    protected $table = 'tour_availabilities';

    protected $fillable = [
        'tour_package_id', 'available_date', 'stock', 'is_available'
    ];

    protected $casts = [
        'available_date' => 'date',
        'is_available' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
