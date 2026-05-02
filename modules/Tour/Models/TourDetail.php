<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;

class TourDetail extends Model
{
    public $table = "tour_details";

    protected $fillable = ['tour_package_id', 'label', 'content', 'order'];

    public function package()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
