<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;

class TourLabel extends Model
{
    protected $table = 'tour_labels';

    protected $fillable = ['name', 'slug', 'icon', 'color'];

    public function packages()
    {
        return $this->belongsToMany(TourPackage::class, 'tour_package_label');
    }
}
