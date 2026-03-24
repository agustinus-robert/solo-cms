<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Web\Models\Chart;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Traits\SaleTrait;

use App\Events\ProductStockUpdated;

class CartController extends Controller
{
    use SaleTrait;

    private function broadcastStock($productId, $variantCode = null)
    {
        $product = Product::with('outlets')->find($productId);
        if (!$product) return;

        $outletId = $product->outlets->first()?->id;
        $stockInDb = (int) $this->getAvailableStock($productId, $outletId, $variantCode);

        $totalBookedGlobal = Chart::all()->sum(function($cart) use ($productId, $variantCode) {
            $cItems = $cart->items ?? [];
            $sum = 0;
            foreach ($cItems as $ci) {
                if ($ci['product_id'] == $productId && ($ci['code'] ?? '') == ($variantCode ?? '')) {
                    $sum += (int)$ci['qty'];
                }
            }
            return $sum;
        });

        $remainingVirtualStock = max(0, $stockInDb - $totalBookedGlobal);

        \Log::info("Memicu Broadcast:", [
            'productId' => $productId,
            'variantCode' => $variantCode,
            'remainingStock' => $remainingVirtualStock
        ]);

        broadcast(new ProductStockUpdated($productId, $remainingVirtualStock, $variantCode));
    }

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

        $this->broadcastStock($productId, $finalCode);

        return response()->json([
            'success' => true,
            'cart_count' => count($items),
            'message' => 'Berhasil ditambahkan'
        ]);
    }


    public function addOnDetail(Request $request)
    {
        $productId   = $request->id;
        $variantCode = $request->variant_code;
        $qtyInput    = (int) ($request->qty ?? 1);

        $product = Product::with(['variant', 'outlets'])->findOrFail($productId);
        $outletId = $product->outlets->first()?->id ?? 1;

        $finalPrice  = (float) $product->price;
        $finalName   = $product->name;
        $finalCode   = $product->sku;
        $targetVariant = null;

        if ($product->variant->count() > 0) {
            if (!$variantCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih varian terlebih dahulu.'
                ], 422);
            }

            foreach ($product->variant as $v) {
                $rawData = is_string($v->product_variant) ? json_decode($v->product_variant, true) : $v->product_variant;
                foreach ($rawData as $sub) {
                    if ($sub['code'] == $variantCode) {
                        $targetVariant = $sub;
                        $finalPrice    = (float) $sub['price'];
                        $finalName     = $product->name . " (" . ($sub['name'] ?? '') . ")";
                        $finalCode     = $sub['code'];
                        break 2;
                    }
                }
            }

            if (!$targetVariant) {
                return response()->json(['success' => false, 'message' => 'Varian tidak ditemukan.'], 404);
            }
        }

        $stockInDb = (int) $this->getAvailableStock($productId, $outletId, $finalCode);
        $totalBookedGlobal = Chart::all()->sum(function($cart) use ($productId, $finalCode) {
            $sum = 0;
            foreach ($cart->items ?? [] as $ci) {
                if ($ci['product_id'] == $productId && ($ci['code'] ?? '') == $finalCode) {
                    $sum += (int)$ci['qty'];
                }
            }
            return $sum;
        });

        $itemKey = $variantCode ? "{$productId}_{$variantCode}" : "{$productId}";

        $search = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::firstOrNew($search);
        $items = $cartRecord->items ?? [];

        $qtyInMyCart = isset($items[$itemKey]) ? (int) $items[$itemKey]['qty'] : 0;

        $virtualStock = $stockInDb - ($totalBookedGlobal - $qtyInMyCart);

        if ($qtyInput > $virtualStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi! Sisa tersedia: ' . max(0, $virtualStock)
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
                'variant_code' => $variantCode
            ];
        }

        $items[$itemKey]['subtotal'] = $items[$itemKey]['qty'] * $items[$itemKey]['price'];

        $cartRecord->items = $items;
        $cartRecord->save();

        if (method_exists($this, 'broadcastStock')) {
            $this->broadcastStock($productId, $finalCode);
        }

        $variantModel = ProductVariant::where('product_id', $productId)->first();
        $groupedData = $variantModel ? $variantModel->getGroupedTiers($outletId) : null;

        return response()->json([
            'success' => true,
            'cart_count' => count($items),
            'variants' => $groupedData ? $groupedData['combinations'] : [],
            'message' => $qtyInput > 0 ? 'Berhasil ditambahkan ke keranjang' : 'Stok disinkronkan'
        ]);
    }

   public function checkStock(Request $request)
    {
        $productId = $request->id;
        $product = Product::with(['variant', 'outlets'])->findOrFail($productId);
        $outletId = $product->outlets->first()?->id ?? 1;

        $search = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($search)->first();
        $myItems = $cartRecord ? ($cartRecord->items ?? []) : [];

        $variantModel = ProductVariant::where('product_id', $productId)->first();

        if (!$variantModel) {
            $finalCode = $product->sku;
            $stockInDb = (int) $this->getAvailableStock($productId, $outletId, $finalCode);

            $totalBookedGlobal = Chart::all()->sum(function($cart) use ($productId, $finalCode) {
                $sum = 0;
                foreach ($cart->items ?? [] as $ci) {
                    if ($ci['product_id'] == $productId && ($ci['code'] ?? '') == $finalCode) {
                        $sum += (int)$ci['qty'];
                    }
                }
                return $sum;
            });

            // 🔥 Samakan logic key dengan fungsi add()
            $itemKey = (string)$productId;
            $qtyInMyCart = isset($myItems[$itemKey]) ? (int) $myItems[$itemKey]['qty'] : 0;

            return response()->json([
                'success' => true,
                'main_stock' => max(0, $stockInDb - ($totalBookedGlobal - $qtyInMyCart)),
                'variants' => []
            ]);
        }

        // --- CASE 2: ADA VARIANT ---
        $groupedData = $variantModel->getGroupedTiers($outletId);
        $combinations = $groupedData ? $groupedData['combinations'] : [];

        foreach ($combinations as &$comp) {
            $vCode = $comp['code'];

            $totalBooked = Chart::all()->sum(function($cart) use ($productId, $vCode) {
                $sum = 0;
                foreach ($cart->items ?? [] as $ci) {
                    if ($ci['product_id'] == $productId && ($ci['code'] ?? '') == $vCode) {
                        $sum += (int)$ci['qty'];
                    }
                }
                return $sum;
            });

            $itemKey = "{$productId}_{$vCode}";
            $qtyInMyCart = isset($myItems[$itemKey]) ? (int) $myItems[$itemKey]['qty'] : 0;
            $comp['qty'] = max(0, (int)$comp['qty'] - ($totalBooked - $qtyInMyCart));
        }

        $mainStock = (count($combinations) > 0) ? $combinations[0]['qty'] : $product->stock;

        return response()->json([
            'success' => true,
            'variants' => $combinations,
            'main_stock' => (int) $mainStock
        ]);
    }

    public function renderDropdown()
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($identifier)->first();

        $items = ($cartRecord && is_array($cartRecord->items)) ? $cartRecord->items : [];

        if (!empty($items)) {
            $productIds = array_unique(array_column($items, 'product_id'));

            $productData = Product::whereIn('id', $productIds)
                ->select('id', 'location', 'image_name')
                ->get()
                ->keyBy('id');

            foreach ($items as $key => &$item) {
                $pId = $item['product_id'];
                $item['location'] = $productData[$pId]->location ?? null;
                $item['image_name'] = $productData[$pId]->image_name ?? null;
            }
        }

        $total = array_reduce($items, function($carry, $item) {
            return $carry + (($item['price'] ?? 0) * ($item['qty'] ?? 0));
        }, 0);

        return view('web::components.chart-version.electro.chart-corner', [
            'items' => $items,
            'total' => $total
        ])->render();
    }

    public function remove($id)
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($identifier)->first();

        if ($cartRecord) {
            $items = $cartRecord->items ?? [];

            if (isset($items[$id])) {
                $targetItem = $items[$id];

                unset($items[$id]);
                $cartRecord->items = $items;
                $cartRecord->save();

                $this->broadcastStock($targetItem['product_id'], $targetItem['code'] ?? null);

                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus',
                    'cart_count' => count($items)
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan'
        ], 404);
    }

    public function detail(Request $request)
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $cartRecord = Chart::where($identifier)->first();
        $items = $cartRecord ? ($cartRecord->items ?? []) : [];

        if (!empty($items)) {
            $productIds = array_column($items, 'product_id');
            $productData = Product::whereIn('id', $productIds)
                ->select('id', 'location', 'image_name')
                ->get()
                ->keyBy('id');

            foreach ($items as $key => &$item) {
                $pId = $item['product_id'];
                $item['location'] = $productData[$pId]->location ?? null;
                $item['image_name'] = $productData[$pId]->image_name ?? null;
            }
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['qty'] ?? 0);
        }

        return view('web::electro.cart.detail', [
            'items' => $items,
            'subtotal' => $subtotal,
            'canEdit' => false
        ]);
    }
}
