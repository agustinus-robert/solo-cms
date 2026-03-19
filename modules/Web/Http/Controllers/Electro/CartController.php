<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Web\Models\Chart;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $productId = $request->id;
        $variantId = $request->variant_id;
        $qtyInput  = (int) ($request->qty ?? 1);

        $product = Product::with('variant')->findOrFail($productId);

        if ($product->variant->count() > 0 && is_null($variantId)) {
            $variants = $product->variant->map(function($v) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;
                $code = $rawData[0]['code'] ?? null;

                $v->available_qty = \Modules\Poz\Models\ProductVariant::getAvailableQty($code);
                return $v;
            });

            return response()->json([
                'success' => false,
                'status' => 'NEED_VARIANT',
                'variants' => $variants
            ]);
        }

        if (Auth::check()) {
            $search = ['user_id' => Auth::id()];
            $defaults = ['session_id' => null];
        } else {
            $search = ['session_id' => session()->getId()];
            $defaults = ['user_id' => null];
        }

        $cartRecord = Chart::firstOrNew($search, $defaults);
        $items = $cartRecord->items ?? [];

        $finalName  = $product->name;
        $finalCode  = $product->code ?? "-";
        $finalPrice = (float) $product->wholesale;
        $realStock  = 0;

        if ($variantId) {
            $v = $product->variant->where('id', $variantId)->first();
            if ($v) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;
                if (is_array($rawData) && count($rawData) > 0) {
                    $target = $rawData[0];
                    $finalName  = $product->name . " (" . ($target['name'] ?? '') . ")";
                    $finalCode  = $target['code'] ?? $product->code;
                    $finalPrice = (float) ($target['price'] ?? $product->wholesale);
                    $realStock  = (int) ($target['qty'] ?? 0);
                }
            }
        } else {
            $realStock = (int) ($product->stock ?? 0);
        }

        $totalReserved = Chart::where('items', 'LIKE', '%"code":"' . $finalCode . '"%')
            ->get()
            ->sum(function ($c) use ($finalCode) {
                $cartItems = $c->items ?? [];
                return collect($cartItems)->where('code', $finalCode)->sum('qty');
            });

        $itemKey = $variantId ? "{$productId}_{$variantId}" : $productId;
        $qtyInMyCart = isset($items[$itemKey]) ? (int) $items[$itemKey]['qty'] : 0;

        $availableForMe = $realStock - ($totalReserved - $qtyInMyCart);

        if (($qtyInMyCart + $qtyInput) > $availableForMe) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak cukup! Sisa tersedia: ' . $availableForMe
            ], 422);
        }

        if (isset($items[$itemKey])) {
            $items[$itemKey]['qty'] += $qtyInput;
        } else {
            $items[$itemKey] = [
                'product_id' => (int) $product->id,
                'code'       => $finalCode,
                'name'       => $finalName,
                'qty'        => $qtyInput,
                'price'      => $finalPrice,
            ];
        }

        $items[$itemKey]['subtotal'] = $items[$itemKey]['qty'] * $items[$itemKey]['price'];

        $cartRecord->items = $items;
        $cartRecord->save();

        return response()->json([
            'success' => true,
            'cart_count' => count($items),
            'message' => 'Berhasil ditambahkan ke keranjang'
        ]);
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
