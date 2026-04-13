<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Account\Models\User;
use Modules\HRMS\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserOutlet extends Model
{
    use HasFactory, HasAuditLog;

    public $timestamps = false;
    public $table = "user_employee_outlets";

    protected $fillable = [
        'empl_id',
        'outlet_id',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class, 'empl_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
