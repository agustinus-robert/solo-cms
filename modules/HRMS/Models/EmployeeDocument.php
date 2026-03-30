<?php

namespace Modules\HRMS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Restorable\Restorable;
use App\Models\Traits\Searchable\Searchable;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\Core\Models\Traits\Approvable\Approvable;
use Modules\HRMS\Enums\DeductionTypeEnum;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use Restorable, Searchable, Approvable, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'empl_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empl_id',
        'name',
        'file'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [];

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
    public $searchable = [];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [];

    /**
     * This belongs to employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empl_id')->withDefault()->withTrashed();
    }
}
