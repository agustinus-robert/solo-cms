<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $table = 'tours';

    protected $fillable = [
        'title', 'slug', 'location', 'overview',
        'opening_hours', 'base_price', 'highlights'
    ];

    // Mengubah JSON highlights menjadi array otomatis
    protected $casts = [
        'highlights' => 'array',
    ];

    /**
     * Relasi ke paket-paket tour
     */
    public function packages(): HasMany
    {
        return $this->hasMany(TourPackage::class);
    }

    /**
     * Relasi ke album foto
     */
    public function photos(): HasMany
    {
        return $this->hasMany(TourPhoto::class);
    }
}
