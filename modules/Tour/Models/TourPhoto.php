<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPhoto extends Model
{
    use SoftDeletes;
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
