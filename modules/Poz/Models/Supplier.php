<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Userstamps\Userstamps;
use Modules\Account\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes, Userstamps;

    public $table = "ref_suppliers";

    protected $fillable = [
        'code',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'location',
        'image_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_suppliers', 'supplier_id', 'outlet_id');
    }

    public function stock(){
        return $this->hasMany(SupplierSchedule::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
