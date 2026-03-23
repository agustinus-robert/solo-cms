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

class ProductVariant extends Model
{
    use HasFactory, HasAuditLog, Restorable, SoftDeletes, Userstamps, SaleTrait;

    public $table = "product_master_variants";

    protected $fillable = [
        'product_id',
        'product_variant',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

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

    public function getGroupedTiers($outletId = null)
    {
        if (!$outletId && $this->product) {
            $outlet = $this->product->outlets->first();
            $outletId = $outlet ? $outlet->id : null;
        }

        $rawData = $this->product_variant;
        $variants = is_string($rawData) ? json_decode($rawData, true) : $rawData;
        if (empty($variants) || !is_array($variants)) return null;

        $hasTier = collect($variants)->contains(fn($item) => isset($item['tier_1_id']));
        if (!$hasTier) return null;

        $tierTransactionIds = collect($variants)
            ->flatMap(fn($item) => [$item['tier_1_id'] ?? null, $item['tier_2_id'] ?? null])
            ->filter()->unique();

        $tierTransactions = TierTransaction::with('tiers')
            ->whereIn('id', $tierTransactionIds)
            ->get()
            ->keyBy('id');

        $activeVariants = collect($variants)
            ->filter(fn($item) => empty($item['deleted_at']) && ($item['status'] ?? '') === 'active');

        $allCarts = Chart::all();

        $grouped = [
            'labels' => [],
            'combinations' => []
        ];

        foreach (['tier_1', 'tier_2'] as $key) {
            $ids = $activeVariants->pluck($key . '_id')->filter()->unique();
            if ($ids->isNotEmpty()) {
                $sampleId = $ids->first();
                $transaction = $tierTransactions[$sampleId] ?? null;
                $parentLabel = ($transaction && $transaction->tiers) ? $transaction->tiers->name : 'Pilihan';

                $grouped['labels'][] = [
                    'parent_name' => $parentLabel,
                    'items' => $ids->map(fn($id) => [
                        'id' => $id,
                        'name' => $tierTransactions[$id]->name ?? 'Unknown'
                    ])->values()
                ];
            }
        }

        $grouped['combinations'] = $activeVariants->map(function ($item) use ($outletId, $allCarts) {
            $stockFromGudang = $outletId
                ? (int) $this->getAvailableStock($this->product_id, $outletId, $item['code'])
                : 0;

            $reservedInChart = 0;
            foreach ($allCarts as $cart) {
                $cartItems = $cart->items;
                if (!is_array($cartItems)) continue;

                foreach ($cartItems as $cartItem) {
                    $cCode = $cartItem['code'] ?? ($cartItem['variant_code'] ?? null);
                    if ($cCode === $item['code']) {
                        $reservedInChart += (int) ($cartItem['qty'] ?? 0);
                    }
                }
            }

            $finalAvailable = $stockFromGudang - $reservedInChart;

            return [
                'code'  => $item['code'],
                'price' => (int)$item['price'],
                'qty'   => $finalAvailable < 0 ? 0 : $finalAvailable,
                't1'    => $item['tier_1_id'] ?? null,
                't2'    => $item['tier_2_id'] ?? null,
            ];
        })->values();

        return $grouped;
    }
}
