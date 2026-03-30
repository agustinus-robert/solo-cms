<?php

namespace Modules\HRMS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Searchable\Searchable;
use App\Models\Casts\UserAgent\UserAgentCast;
use Modules\Core\Enums\WorkLocationEnum;
use Illuminate\Database\Eloquent\SoftDeletes; 

class EmployeeTeacherScanLog extends Model
{
    use Searchable, SoftDeletes;

    protected $connection = 'mysql';

    /**
     * The collection associated with the model.
     */
    protected $table = 'empl_teacher_scan_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empl_id',
        'ip',
        'location',
        'type',
        'latlong',
        'user_agent',
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'location' => WorkLocationEnum::class,
        'latlong' => 'array',
        'user_agent' => UserAgentCast::class,
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
     * The attributes that are searchable.
     */
    public $searchable = [
        'employee.user.name',
        'ip',
        'user_agent'
    ];

    /**
     * This belongs to employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empl_id', 'id');
    }

    public function historyScanlog(){
        return $this->hasMany(EmployeeTeacherHistoryScanLog::class, 'scanlog_id')->withTrashed();
    }

    /**
     * When position of department.
     */
    public function scopeWhenPositionOfDepartment($query, $dep, $pos)
    {
        return $query->when(
            $dep,
            fn($q1) =>
            $q1->whereHas('employee.contract.position.position', fn($q3) => $q3->whereIn('dept_id', (array) $dep))->when(
                $pos,
                fn($q2) =>
                $q2->whereHas('employee.contract.position', fn($q3) => $q3->whereIn('position_id', (array) $pos))
            )
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    public function historyLog(){
        return $this->hasOne(EmployeeTeacherScanLog::class, 'empl_id');
    }
}
