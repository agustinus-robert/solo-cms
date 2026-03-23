<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Poz\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with('variant')->find($id);
        $groupedData = $product->variant->first()->getGroupedTiers();
        $canEdit = false;

        return view('web::electro.shop.show', compact('product', 'canEdit', 'groupedData'));
    }
}
