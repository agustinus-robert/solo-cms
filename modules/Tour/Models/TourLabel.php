<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourLabel extends Model
{
    use SoftDeletes;
    protected $table = 'tour_labels';

    protected $fillable = ['name', 'slug', 'icon', 'color'];

    public function packages()
    {
        return $this->belongsToMany(TourPackage::class, 'tour_package_label');
    }
}
