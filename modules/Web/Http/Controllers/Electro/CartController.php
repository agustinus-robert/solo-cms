<?php

namespace Modules\Web\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Poz\Models\Chart;
use Modules\Poz\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $productId = $request->id;
        $product = Product::findOrFail($productId);

        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::firstOrNew($identifier);

        $items = $cartRecord->items ?? [];

        if (isset($items[$productId])) {
            $items[$productId]['qty']++;
        } else {
            $items[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $product->wholesale,
                'image' => asset('uploads/'.$product->location.'/'.$product->image_name)
            ];
        }

        $cartRecord->items = $items;
        $cartRecord->save();

        return response()->json(['success' => true]);
    }

    public function renderDropdown()
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($identifier)->first();

        $items = $cartRecord ? $cartRecord->items : [];

        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }

        return view('web::components.chart-version.electro.chart-corner', [
            'items' => $items,
            'total' => $total
        ])->render();
    }
}
