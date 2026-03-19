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

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory, HasAuditLog, Restorable, SoftDeletes, Userstamps;

    public $table = "product_master_variants";

    protected $fillable = [
        'product_id',
        'product_variant',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function getAvailableQty($variantCode)
    {
        $variant = self::where('product_variant', 'LIKE', '%' . $variantCode . '%')->first();
        if (!$variant) return 0;

        $rawData = is_string($variant->product_variant) ? json_decode($variant->product_variant, true) : $variant->product_variant;
        $realQty = (int) ($rawData[0]['qty'] ?? 0);

        $allCarts = \DB::table('product_carts')
            ->where('items', 'LIKE', '%' . $variantCode . '%')
            ->get();

        $totalReserved = 0;

        foreach ($allCarts as $cart) {
            $items = json_decode($cart->items, true);
            if (!is_array($items)) continue;

            foreach ($items as $item) {
                if (isset($item['code']) && $item['code'] === $variantCode) {
                    $totalReserved += (int) $item['qty'];
                }
            }
        }

        $available = $realQty - $totalReserved;

        return $available < 0 ? 0 : $available;
    }
}
