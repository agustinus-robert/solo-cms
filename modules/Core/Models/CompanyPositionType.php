<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable\Cacheable;
use App\Models\Traits\Searchable\Searchable;

class CompanyPositionType extends Model
{
    use Cacheable, Searchable;

    protected $table = 'cmp_position_types';

    protected $fillable = [
        'kd',
        'name',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $searchable = [
        'kd',
        'name',
        'category',
    ];

    public function positions()
    {
        return $this->hasMany(CompanyPosition::class, 'position_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFindByKd($query, $kd)
    {
        return $query->where('kd', $kd)->firstOrFail();
    }
}
