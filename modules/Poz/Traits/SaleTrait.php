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
    /**
     * Mengambil stok tersedia berdasarkan Produk, Outlet, dan Variant Code.
     */
    public function getAvailableStock($productId, $outletId, $variantCode = null)
    {
        $today = Carbon::today();

        $baseStock = ProductStock::where('product_id', $productId)
            ->when($variantCode, function ($q) use ($variantCode) {
                return $q->where('variant_code', $variantCode);
            });

        $stockIn = (clone $baseStock)
            ->where('status', 'plus')
            ->whereHasMorph('stockable', [Purchase::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $stockOut = (clone $baseStock)
            ->where('status', 'minus')
            ->whereHasMorph('stockable', [SaleDirect::class, Sale::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $qtyInCart = SaleDirectCart::where('product_id', $productId)
            ->when($variantCode, function ($q) use ($variantCode) {
                return $q->where('variant_code', $variantCode);
            })
            ->whereDate('created_at', $today)
            ->whereHas('outlets', function ($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })
            ->sum('qty');

        return (float)($stockIn - $stockOut - $qtyInCart);
    }

    /**
     * Menghitung total penjualan dari item yang dipilih.
     */
    public function calculateSaleTotals($items, $discount = 0)
    {
        $subtotal = collect($items)->sum(fn($item) => (float)$item['qty'] * (float)$item['price']);

        $afterDiscount = max(0, $subtotal - (float)$discount);
        $ppn = $afterDiscount * 0.11;

        return [
            'subtotal'    => (float)$subtotal,
            'discount'    => (float)$discount,
            'ppn'         => (float)$ppn,
            'grand_total' => (float)($afterDiscount + $ppn)
        ];
    }
}
