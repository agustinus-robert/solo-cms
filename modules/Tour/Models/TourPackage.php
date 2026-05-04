<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPackage extends Model
{
    use SoftDeletes;
    public $table = "tour_packages";

    protected $fillable = ['tour_id', 'package_name', 'price_per_person'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Relasi ke detail fleksibel (Syarat, Itinerary, dll)
     */
    public function details(): HasMany
    {
        return $this->hasMany(TourDetail::class)->orderBy('order');
    }

    /**
     * Relasi ke jadwal ketersediaan
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(TourAvailability::class);
    }


    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(TourLabel::class, 'tour_package_label');
    }

    /**
     * Relasi ke waktu
     */
    public function times(): HasMany
    {
        return $this->hasMany(TourPackageTime::class);
    }
}
