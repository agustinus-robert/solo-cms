<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Modules\Poz\Models\ProductVariantAdjustment;
use \Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\TierTransaction;
use Modules\Poz\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVariantController extends Controller
{
    public function show($product)
    {
        $product = Product::with(['metas', 'brand', 'category'])->findOrFail($product);

        $tierCount = (int) $product->getMeta('tier_count', 0);
        $tier1Name = $product->getMeta('tier_name_1', null);
        $tier2Name = $product->getMeta('tier_name_2', null);

        $tier1 = null;
        $tier2 = null;
        $options1 = collect([]);
        $options2 = collect([]);

        if ($tierCount > 0) {
            if (is_numeric($tier1Name)) {
                $tier1 = Tier::find($tier1Name);
                if ($tier1) {
                    $options1 = TierTransaction::where('tier_id', $tier1->id)->get();
                }
            }

            if ($tierCount == 2 && is_numeric($tier2Name)) {
                $tier2 = Tier::find($tier2Name);
                if ($tier2) {
                    $options2 = TierTransaction::where('tier_id', $tier2->id)->get();
                }
            }
        }

        $existing = ProductVariant::where('product_id', $product->id)->first();
        $savedVariants = $existing ? json_decode($existing->product_variant, true) : [];

        return view('poz::transaction.product.show', [
            'product'       => $product,
            'tierCount'     => $tierCount,
            'tier1Name'     => $tier1Name,
            'tier2Name'     => $tier2Name,
            'options1'      => $options1,
            'options2'      => $options2,
            'tier1'         => $tier1,
            'tier2'         => $tier2,
            'savedVariants' => $savedVariants,
            'action'        => 'Show'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $productId = $request->product_id;
            $hasVariant = $request->has_variant == 'yes';

            $existingRecord = ProductVariant::where('product_id', $productId)->first();
            $oldVariants = $existingRecord ? json_decode($existingRecord->product_variant, true) : [];
            $oldQtyMap = collect($oldVariants)->pluck('qty', 'code')->toArray();

            $newVariants = [];
            $incomingCodes = $request->codes ?? [];

            $logStock = function($pId, $code, $newQty, $oldQty) {
                if ($newQty == $oldQty) return;

                $diff = $newQty - $oldQty;
                $status = $diff > 0 ? 'plus' : 'minus';
                $absQty = abs($diff);

                $adjustment = ProductVariantAdjustment::create([
                    'product_id' => $pId,
                    'code'       => $code,
                    'qty'        => $absQty,
                    'status'     => $status,
                    'created_by' => auth()->id(),
                ]);

                ProductStock::create([
                    'product_id'     => $pId,
                    'supplier_id'    => null,
                    'stockable_id'   => $adjustment->id,
                    'stockable_type' => \Modules\Poz\Models\ProductVariantAdjustment::class,
                    'status'         => $status,
                    'qty'            => $absQty,
                    'variant_code'   => $code,
                    'wholesale'      => Product::find($pId)->wholesale ?? 0,
                    'product_status' => 'active',
                    'created_by'     => auth()->id(),
                ]);
            };

            if ($hasVariant && !empty($request->tier_1_ids)) {
                foreach ($request->tier_1_ids as $index => $t1_id) {
                    if (empty($t1_id)) continue;

                    $t1_model = TierTransaction::find($t1_id);
                    $t2_id = $request->tier_2_ids[$index] ?? null;
                    $t2_model = $t2_id ? TierTransaction::find($t2_id) : null;
                    $variantName = $t1_model->name . ($t2_model ? ' - ' . $t2_model->name : '');

                    $currentCode = $request->codes[$index];
                    $newQty = (int)($request->qtys[$index] ?? 0);
                    $oldQty = (int)($oldQtyMap[$currentCode] ?? 0);

                    // Eksekusi Log Stok
                    $logStock($productId, $currentCode, $newQty, $oldQty);

                    $newVariants[] = [
                        'tier_1_id' => $t1_id,
                        'tier_2_id' => $t2_id,
                        'name'      => $variantName,
                        'code'      => $currentCode,
                        'price'     => $request->prices[$index] ?? 0,
                        'qty'       => $newQty,
                        'alert_qty' => $request->alert_qtys[$index] ?? 0,
                        'status'    => 'active',
                        'variant_type' => 'with_variant',
                        'deleted_at' => null
                    ];
                }
            }
            else {
                $currentCode = $request->product_code;
                $newQty = (int)($request->single_qty ?? 0);
                $oldQty = (int)($oldQtyMap[$currentCode] ?? 0);

                // Eksekusi Log Stok Single
                $logStock($productId, $currentCode, $newQty, $oldQty);

                $newVariants[] = [
                    'tier_1_id' => null,
                    'tier_2_id' => null,
                    'name'      => 'No Variant',
                    'code'      => $currentCode,
                    'price'     => $request->single_price ?? 0,
                    'qty'       => $newQty,
                    'alert_qty' => $request->single_alert_qty ?? 0,
                    'status'    => 'active',
                    'variant_type' => 'no_variant',
                    'deleted_at' => null
                ];
                $incomingCodes[] = $currentCode;
            }

            // Handle deleted variants
            foreach ($oldVariants as $old) {
                if (!in_array($old['code'], $incomingCodes)) {
                    if ($old['qty'] > 0 && ($old['status'] ?? 'active') !== 'deleted') {
                        $logStock($productId, $old['code'], 0, $old['qty']);
                    }

                    $old['status'] = 'deleted';
                    $old['qty']    = 0;
                    if (empty($old['deleted_at'])) {
                        $old['deleted_at'] = now()->format('Y-m-d H:i:s');
                    }
                    $newVariants[] = $old;
                }
            }

            ProductVariant::updateOrCreate(
                ['product_id' => $productId],
                [
                    'product_variant' => json_encode($newVariants),
                    'updated_by'      => auth()->id(),
                    'created_by'      => auth()->id(),
                ]
            );

            return redirect()->back()->with('msg-sukses', 'Varian dan Stok berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('msg-gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
