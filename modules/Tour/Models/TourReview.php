<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tour_id',
        'user_id',
        'rating',
        'comment'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
