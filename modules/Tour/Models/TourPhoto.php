<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;

class TourPhoto extends Model
{
    protected $table = 'tour_photos';

    protected $fillable = ['tour_id', 'image_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
