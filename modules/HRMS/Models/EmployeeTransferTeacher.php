<?php

namespace Modules\HRMS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Searchable\Searchable;
use App\Models\Casts\UserAgent\UserAgentCast;
use Modules\HRMS\Enums\TransferTypeEnum;

class EmployeeTransferTeacher extends Model
{
    use Searchable;

    /**
     * The collection associated with the model.
     */
    protected $table = 'empl_transfer_teachers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empl_id',
        'from',
        'to',
        'rate',
        'price',
        'hour_rate',
        'hour_price',
        'start_date',
        'end_date',
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'from' => TransferTypeEnum::class,
        'to' => TransferTypeEnum::class,
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
        'employee.user.name'
    ];

    /**
     * This belongs to employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empl_id', 'id');
    }
}
