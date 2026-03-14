<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;

use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPostImage extends Model
{
    use HasFactory;

    public $table = "cms_post_image";

    public function categories()
    {
        return $this->belongsToMany(CmsCategory::class, 'post_image_has_category', 'post_image_id', 'category_id');
    }
}
