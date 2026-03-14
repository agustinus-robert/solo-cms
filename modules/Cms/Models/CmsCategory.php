<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsCategory extends Model
{
    use HasFactory;

    public $table = "cms_category";

    public function posts()
    {
        return $this->belongsToMany(
            CmsPost::class,
            'cms_post_has_category', // Pivot table
            'tags_id',               // Foreign key di pivot mengarah ke CmsCategory
            'post_id'                // Foreign key di pivot mengarah ke CmsPost
        );
    }

    public function menuCategory()
    {
        return $this->belongsTo(CmsMenuCategory::class, 'id_menu_category');
    }
}
