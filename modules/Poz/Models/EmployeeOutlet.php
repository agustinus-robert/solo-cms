<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeOutlet extends Model
{
    use HasFactory, HasAuditLog;

    public $timestamps = false;
    public $table = "user_employee_outlets";

    protected $fillable = [
        'user_id',
        'outlet_id',
    ];
}
