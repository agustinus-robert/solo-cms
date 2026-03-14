<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;

use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPostVideo extends Model
{
    use HasFactory;

    public $table = "cms_post_video";
}
