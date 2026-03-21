<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Web\Models\Chart;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Traits\SaleTrait;

class CartController extends Controller
{
    use SaleTrait;

    public function add(Request $request)
    {
        $productId = $request->id;
        $variantId = $request->variant_id;
        $qtyInput  = (int) ($request->qty ?? 1);

        $product = Product::with(['variant', 'outlets'])->findOrFail($productId);
        $outletId = $product->outlets->first()?->id;

        if ($product->variant->count() > 0 && is_null($variantId)) {
            $variants = $product->variant->map(function($v) use ($outletId) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;

                if (is_array($rawData)) {
                    foreach ($rawData as &$item) {
                        $dbStock = (float) $this->getAvailableStock($v->product_id, $outletId, $item['code'] ?? null);

                        $globalBooked = Chart::all()->sum(function($cart) use ($v, $item) {
                            $cItems = $cart->items ?? [];
                            $cKey = $v->id . "_" . ($item['code'] ?? '');
                            foreach($cItems as $key => $ci) {
                                if($ci['product_id'] == $v->product_id && ($ci['code'] ?? '') == ($item['code'] ?? '')) {
                                    return (int)$ci['qty'];
                                }
                            }
                            return 0;
                        });

                        $item['real_stock'] = max(0, $dbStock - $globalBooked);
                    }
                }

                $itemArray = $v->toArray();
                $itemArray['decoded_variants'] = $rawData;
                return $itemArray;
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

        $finalName   = $product->name;
        $finalCode   = $product->code ?? "-";
        $finalPrice  = (float) $product->wholesale;
        $variantCode = null;

        if ($variantId) {
            $v = ProductVariant::find($variantId);
            if ($v) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;
                $target = $rawData[0];
                if ($request->has('variant_code')) {
                    foreach($rawData as $sub) {
                        if ($sub['code'] == $request->variant_code) {
                            $target = $sub;
                            break;
                        }
                    }
                }
                $finalName   = $product->name . " (" . ($target['name'] ?? '') . ")";
                $finalCode   = $target['code'] ?? $product->code;
                $variantCode = $target['code'] ?? null;
                $finalPrice  = (float) ($target['price'] ?? $product->wholesale);
            }
        }

        $stockInDb = (int) $this->getAvailableStock($productId, $outletId, $variantCode);

        $totalBookedGlobal = Chart::all()->sum(function($cart) use ($productId, $finalCode) {
            $cItems = $cart->items ?? [];
            $sum = 0;
            foreach($cItems as $ci) {
                if($ci['product_id'] == $productId && ($ci['code'] ?? '') == $finalCode) {
                    $sum += (int)$ci['qty'];
                }
            }
            return $sum;
        });

        $itemKey = $variantId ? "{$productId}_{$variantId}" : $productId;
        $qtyInMyCart = isset($items[$itemKey]) ? (int) $items[$itemKey]['qty'] : 0;

        $virtualStock = $stockInDb - ($totalBookedGlobal - $qtyInMyCart);

        if ($qtyInput > $virtualStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak cukup! Sisa: ' . max(0, $virtualStock)
            ], 422);
        }

        if (isset($items[$itemKey])) {
            $items[$itemKey]['qty'] += $qtyInput;
        } else {
            $items[$itemKey] = [
                'product_id' => (int) $product->id,
                'variant_id' => $variantId,
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
            'message' => 'Berhasil ditambahkan'
        ]);
    }

    public function renderDropdown()
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($identifier)->first();
        $items = $cartRecord ? $cartRecord->items : [];
        $total = 0;
        if(is_array($items)) {
            foreach ($items as $item) { $total += $item['price'] * $item['qty']; }
        }
        return view('web::components.chart-version.electro.chart-corner', ['items' => $items, 'total' => $total])->render();
    }
}
