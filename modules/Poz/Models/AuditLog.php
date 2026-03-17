<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog; // Import Trait-nya
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasAuditLog; // Pakai di sini

    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];
}
