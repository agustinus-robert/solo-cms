<?php

namespace App\Observers;

use Modules\Poz\Models\Product;
use Modules\Web\Models\Chart;
use App\Events\ProductStockUpdated;
use Modules\Poz\Traits\SaleTrait;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    use SaleTrait;

    public function updated(Product $product)
    {
        $this->broadcastAll($product);
    }

    public function broadcastAll(Product $product)
    {
        $product->load(['variant', 'outlets']);
        $outletId = $product->outlets->first()?->id ?? 1;

        if ($product->variant && $product->variant->count() > 0) {
            foreach ($product->variant as $v) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;
                if (is_array($rawData)) {
                    foreach ($rawData as $item) {
                        $this->executeBroadcast($product->id, $outletId, $item['code'] ?? null);
                    }
                }
            }
        } else {
            $this->executeBroadcast($product->id, $outletId, $product->sku);
        }
    }

    private function executeBroadcast($productId, $outletId, $variantCode)
    {
        $stockInDb = (int) $this->getAvailableStock($productId, $outletId, $variantCode);
        $totalBookedGlobal = Chart::all()->sum(function($cart) use ($productId, $variantCode) {
            $sum = 0;
            foreach ($cart->items ?? [] as $ci) {
                if ($ci['product_id'] == $productId && ($ci['code'] ?? '') == ($variantCode ?? '')) {
                    $sum += (int)$ci['qty'];
                }
            }
            return $sum;
        });

        $remainingVirtualStock = max(0, $stockInDb - $totalBookedGlobal);

        broadcast(new ProductStockUpdated($productId, $remainingVirtualStock, $variantCode));
    }
}
