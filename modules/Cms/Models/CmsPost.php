<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;

use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPost extends Model
{
    use HasFactory, Metable;

    public $table = "cms_post";
    protected $metaTable = 'cms_post_meta';

    public function categories()
    {
        return $this->belongsToMany(CmsCategory::class, 'cms_post_has_category', 'post_id', 'tags_id');
    }

}
