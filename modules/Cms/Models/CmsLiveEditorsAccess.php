<?php

namespace Modules\Cms\Models;

use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Account\Models\User;

class CmsLiveEditorsAccess extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $table = "cms_live_editor_access";

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'user_id', 'status'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
