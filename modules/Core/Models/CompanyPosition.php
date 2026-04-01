<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Metable\Metable;
use App\Models\Traits\Cacheable\Cacheable;
use App\Models\Traits\Searchable\Searchable;
use App\Models\Traits\Restorable\Restorable;

class CompanyPosition extends Model
{
    use Metable, Cacheable, Restorable, Searchable;

    protected $table = 'cmp_positions';

    protected $metaTable = 'cmp_position_meta';
    public $metaKeyName = 'position_id';
    public $hideMeta = true;

    protected $fillable = [
        'kd',
        'name',
        'description',
        'dept_id',
        'position_type_id',
        'level',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public $searchable = [
        'name',
        'kd',
        'description'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function department()
    {
        return $this->belongsTo(CompanyDepartment::class, 'dept_id')->withDefault();
    }

    public function type()
    {
        return $this->belongsTo(CompanyPositionType::class, 'position_type_id');
    }

    public function parents()
    {
        return $this->belongsToMany(self::class, 'cmp_position_trees', 'position_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(
            self::class,
            'cmp_position_trees',
            'parent_id',
            'position_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeVisibility($query, $bool = true)
    {
        return $query->where('is_visible', $bool);
    }

    public function scopeWhenDepartmentId($query, $department)
    {
        return $query->when(
            $department,
            fn ($q) => $q->where('dept_id', $department)
        );
    }

    public function scopeFindByKd($query, $kd)
    {
        return $query->where('kd', $kd)->firstOrFail();
    }
}
