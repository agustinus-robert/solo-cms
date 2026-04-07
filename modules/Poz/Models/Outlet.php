<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Modules\Account\Models\User;
use Modules\HRMS\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory, HasAuditLog;

    public $table = "outlets";

    protected $fillable = [
        'code',
        'name',
        'admin_id',
        'description',
        'location',
        'image_name'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'user_casier_outlets',
            'outlet_id',
            'empl_id'
        )->with('user');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_casier_outlets', 'outlet_id', 'empl_id');
    }
}
