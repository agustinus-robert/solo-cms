<?php

namespace Modules\Poz\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'auditable_id',
        'auditable_type',
        'event',
        'description',
        'old_values',
        'new_values',
        'user_id',
        'url',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }
}
