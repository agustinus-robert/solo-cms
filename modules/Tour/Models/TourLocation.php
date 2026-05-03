<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relasi ke jam keberangkatan paket.
     */
    public function packageTimes()
    {
        return $this->hasMany(TourPackageTime::class, 'tour_location_id');
    }
}
