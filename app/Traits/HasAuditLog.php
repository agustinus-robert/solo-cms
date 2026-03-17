<?php

namespace App\Traits;

use Modules\Poz\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    protected static function bootHasAuditLog()
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    protected function logAudit($event)
    {
        AuditLog::create([
            'auditable_id'   => $this->id,
            'auditable_type' => get_class($this),
            'event'          => $event,
            'user_id'        => Auth::id(),
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
        ]);
    }
}
