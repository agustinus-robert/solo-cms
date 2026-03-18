<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Userstamps\Userstamps;
use Modules\Account\Models\User;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariantAdjustment extends Model
{
    use HasFactory, HasAuditLog, Restorable, SoftDeletes, Userstamps;

    public $table = "product_master_variant_adjustments";

    protected $fillable = [
        'product_id',
        'code',
        'status',
        'qty',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
