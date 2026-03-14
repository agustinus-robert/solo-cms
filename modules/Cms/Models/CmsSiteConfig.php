<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsSiteConfig extends Model
{
    use HasFactory;

    public $table = "cms_post_site_configuration";
}
