<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDirectCustomerDesk extends Model
{
    use HasFactory, HasAuditLog;

    public $table = "sale_direct_customer_desks";

    protected $fillable = [
        'customer_name',
        'desk_name',
        'email',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
