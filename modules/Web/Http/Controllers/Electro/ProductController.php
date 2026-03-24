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
        $firstVariant = $product->variant->first();
        $groupedData = $firstVariant ? $firstVariant->getGroupedTiers() : [
            'labels' => [],
            'combinations' => []
        ];
        $canEdit = false;

        $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->limit(6)
        ->get();


        return view('web::electro.shop.show', compact('product', 'canEdit', 'groupedData', 'relatedProducts'));
    }
}
