<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsMenu extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $table = "cms_menu";
}
