<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;

use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPostSchedule extends Model
{
    use HasFactory, Metable;

    public $table = "cms_schedule_post";
}
