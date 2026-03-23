<?php

namespace Modules\Poz\Models;

use App\Traits\HasAuditLog;
use App\Models\Traits\Restorable\Restorable;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use Modules\Web\Models\Chart;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Userstamps\Userstamps;
use Modules\Account\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\Poz\Models\TierTransaction;
use Modules\Poz\Traits\SaleTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReview extends Model
{
    use HasFactory, HasAuditLog, Restorable, SoftDeletes;

    public $table = "product_reviews";

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'description',
        'rating'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
