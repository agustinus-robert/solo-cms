<?php

namespace Modules\Poz\Traits;

use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Purchase;
use Modules\Poz\Models\Adjustment;
use Modules\Poz\Models\Sale;
use Modules\Poz\Models\SaleDirect;
use Modules\Poz\Models\SaleDirectCart;
use Illuminate\Support\Carbon;

trait SaleTrait
{
    public function getAvailableStock($productId, $outletId)
    {
        $today = Carbon::today();

        $stockIn = ProductStock::where('product_id', $productId)
            ->where('status', 'plus')
            ->whereDate('created_at', $today)
            ->whereHasMorph('stockable', [Purchase::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $stockOut = ProductStock::where('product_id', $productId)
            ->where('status', 'minus')
            ->whereDate('created_at', $today)
            ->whereHasMorph('stockable', [SaleDirect::class, Sale::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $qtyInCart = SaleDirectCart::where('product_id', $productId)
            ->whereDate('created_at', $today)
            ->whereHas('outlets', function ($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })
            ->sum('qty');

        return (float)($stockIn - $stockOut - $qtyInCart);
    }

    public function calculateSaleTotals($items, $discount = 0)
    {
        $subtotal = collect($items)->sum(fn($item) => (float)$item['qty'] * (float)$item['price']);
        $afterDiscount = max(0, $subtotal - (float)$discount);
        $ppn = $afterDiscount * 0.11;

        return [
            'subtotal'    => $subtotal,
            'discount'    => (float)$discount,
            'ppn'         => $ppn,
            'grand_total' => $afterDiscount + $ppn
        ];
    }
}
