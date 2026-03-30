<?php

namespace Modules\HRMS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Searchable\Searchable;
use App\Models\Casts\UserAgent\UserAgentCast;
use Modules\Core\Enums\ScanlogTeacherStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes; 

class EmployeeTeacherHistoryScanLog extends Model
{
    use Searchable, SoftDeletes;
    //testing

    protected $connection = 'mysql';

    /**
     * The collection associated with the model.
     */
    protected $table = 'empl_teacher_history_scan_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'scanlog_id',
        'superior_id',
        'status',
        'description',
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'status' => ScanlogTeacherStatusEnum::class,
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that define value is a instance of carbon.
     */
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * This belongs to employee.
     */
    public function scanlog()
    {
        return $this->belongsTo(EmployeeTeacherScanLog::class, 'scanlog_id');
    }

    public function superior(){
        return $this->belongsTo(Employee::class, 'empl_id', 'id');
    }
}
