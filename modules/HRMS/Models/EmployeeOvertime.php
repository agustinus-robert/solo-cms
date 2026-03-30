<?php

namespace Modules\HRMS\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Restorable\Restorable;
use App\Models\Traits\Searchable\Searchable;
use Illuminate\Support\Facades\DB;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\Traits\Approvable\Approvable;
use Modules\Docs\Models\Traits\Documentable\Documentable;

class EmployeeOvertime extends Model
{
    use Restorable, Searchable, Approvable, Documentable;

    /**
     * The table associated with the model.
     */
    protected $table = 'empl_overtimes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empl_id',
        'schedules',
        'dates',
        'name',
        'description',
        'attachment',
        'scheduled_by',
        'scheduled_at',
        'accepted_at',
        'paidable_at',
        'paid_amount',
        'paid_off_at',
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'schedules'    => 'collection',
        'dates'        => 'collection',
        'deleted_at'   => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'scheduled_at' => 'datetime',
        'accepted_at'  => 'datetime',
        'paidable_at'  => 'datetime',
        'paid_off_at'  => 'datetime'
    ];

    /**
     * The attributes that define value is a instance of carbon.
     */
    protected $dates = [
        'scheduled_at',
        'accepted_at',
        'paidable_at',
        'paid_off_at',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are searchable.
     */
    public $searchable = [
        'description',
        'employee.user.name'
    ];

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

    /**
     * This belongs to employee.
     */
    public function giver()
    {
        return $this->belongsTo(Employee::class, 'empl_id')->withDefault()->withTrashed();
    }

    /**
     * Scope extract date.
     */
    public function scopeWhereExtractedDatesBetween($query, $start_at, $end_at)
    {
        if (version_compare(config('database.connections.mysql.v'), '8.0.0', '>=')) {
            return $query->join(DB::raw("JSON_TABLE(dates, '$[*]' COLUMNS(`_dates` DATE PATH '$.d')) AS _"), "{$this->table}.id", "{$this->table}.id")->whereBetween('_dates', [$start_at, $end_at]);
        } else {
            $length = EmployeeOvertime::selectRaw('MAX(JSON_LENGTH(dates)) as length')->value('length');
            for ($i = 0; $i < $length; $i++) {
                $query = $query->{$i == 0 ? "whereBetween" : "orWhereBetween"}("dates->[{$i}]->d", [$start_at, $end_at]);
            }
            return $query;
        }
    }

    /**
     * Scope when only scheduled.
     */
    public function scopeWhenOnlyScheduled($query, $scheduled = false)
    {
        return $query->when($scheduled, fn($s) => $s->whereNotNull('scheduled_at')->whereNotNull('scheduled_by'));
    }

    /**
     * Scope when only pending.
     */
    public function scopeWhenOnlyPending($query, $pending = false)
    {
        return $query->when($pending, fn($s) => $s->whereHas('approvables', fn($approvable) => $approvable->whereResult(ApprovableResultEnum::PENDING)));
    }

    /**
     * Scope when dates between.
     */
    public function scopeWhenDatesBetween($query, $start_at = false, $end_at = false)
    {
        return $query->where(
            fn($subquery) => $subquery->when($start_at, fn($q) => $q->where(fn($s) => $s->whereRaw('dates->>"$[0].d" >= ?', $start_at)->orWhereJsonContains('dates', ['d' => $start_at])))
                ->when($end_at, fn($q) => $q->where(fn($s) => $s->whereRaw('dates->>"$[0].d" <= ?', $end_at)->orWhereJsonContains('dates', ['d' => $end_at])))
        );
    }

    /**
     * Scope when created at between.
     */
    public function scopeWhenCreatedAtBetween($query, $start_at = false, $end_at = false, $useOr = false)
    {
        return $query->{$useOr ? 'orWhere' : 'where'}(
            fn($subquery) => $subquery->when($start_at, fn($q) => $q->whereDate($this->table . '.created_at', '>=', $start_at))
                ->when($end_at, fn($q) => $q->whereDate($this->table . '.created_at', '<=', $end_at))
        );
    }

    /**
     * Scope when period.
     */
    public function scopeWhenPeriod($query, $start_at = false, $end_at = false)
    {
        return $query->where(
            fn($subquery) => $subquery->whenDatesBetween($start_at, $end_at)->whenCreatedAtBetween($start_at, $end_at, true)
        );
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

    /**
     * Authorization scopes.
     */
    public function can($action)
    {
        return match ($action) {
            'revised' => $this->hasAnyApprovableResultIn('REVISION'),
            'deleted' => !$this->hasApprovables() || ($this->hasApprovables() && !$this->hasAnyApprovableResultIn('REJECT') && ($this->hasAllApprovableResultIn('PENDING') || $this->hasAnyApprovableResultIn('REVISION'))),
            'canceled' => $this->hasApprovables() && $this->approvableTypeIs('approvable') && $this->hasAnyApprovableResultIn('APPROVE'),
            default => false,
        };
    }
}
