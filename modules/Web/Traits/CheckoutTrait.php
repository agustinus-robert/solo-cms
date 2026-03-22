<?php

namespace Modules\Web\Traits;

use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Sale;
use Modules\Web\Models\Chart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

trait CheckoutTrait
{
    public function calculateCheckoutTotals($items, $discount = 0)
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float)($item['qty'] ?? 0) * (float)($item['price'] ?? 0);
        }

        $afterDiscount = max(0, $subtotal - (float)$discount);
        $ppn = $afterDiscount * 0.11;
        $grandTotal = $afterDiscount + $ppn;

        return [
            'sub_total'   => (float)$subtotal,
            'discount'    => (float)$discount,
            'ppn'         => (float)$ppn,
            'grand_total' => (float)$grandTotal
        ];
    }

    public function deductStockFromCart($items, $outletId, $saleId)
    {
        foreach ($items as $item) {
            $newStock = ProductStock::create([
                'product_id'     => $item['product_id'],
                'variant_code'   => $item['code'],
                'qty'            => $item['qty'],
                'status'         => 'minus',
                'stockable_type' => Sale::class,
                'stockable_id'   => $saleId,
            ]);

            $newStock->outlets()->sync([$outletId]);
        }
    }

    public function storeCheckoutItems($sale, $items)
    {
        foreach ($items as $item) {
            $sale->saleItems()->create([
                'product_id'   => $item['product_id'],
                'variant_code' => $item['code'],
                'qty'          => $item['qty'],
                'price'        => $item['price'] ?? 0,
            ]);
        }
    }

    public function getActiveCart()
    {
        $auth = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        return Chart::where($auth)->first();
    }
}
