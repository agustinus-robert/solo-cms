<?php

namespace App\Observers;

use Modules\Poz\Models\ProductVariant;
use App\Observers\ProductObserver;

class ProductVariantObserver
{
    public function updated(ProductVariant $variant)
    {
        $product = $variant->product;
        if ($product) {
            (new ProductObserver())->broadcastAll($product);
        }
    }

    public function created(ProductVariant $variant)
    {
        if ($variant->product) {
            (new ProductObserver())->broadcastAll($variant->product);
        }
    }
}
