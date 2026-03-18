<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Modules\Poz\Models\ProductVariantAdjustment;
use Modules\Poz\Models\TierTransaction;
use Modules\Poz\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVariantController extends Controller
{
    public function show($product)
    {
        $product = Product::with(['metas', 'brand', 'category'])->findOrFail($product);

        $tier1 = '';
        $tier2 = '';
        $tierCount = $product->getMeta('tier_count', 1);
        $tier1Name = $product->getMeta('tier_name_1', 'Pilihan 1');
        $tier2Name = $product->getMeta('tier_name_2', 'Pilihan 2');

        $tier1 = Tier::find($tier1Name);
        $options1 = TierTransaction::whereHas('tiers', function($q) use ($tier1Name) {
                        $q->where('id', $tier1Name);
                    })->get();

        $options2 = [];
        if ($tierCount == 2) {
            $tier2 = Tier::find($tier2Name);
            $options2 = TierTransaction::whereHas('tiers', function($q) use ($tier2Name) {
                            $q->where('id', $tier2Name);
                        })->get();
        }

        $existing = ProductVariant::where('product_id', $product->id)->first();
        $savedVariants = $existing ? json_decode($existing->product_variant, true) : [];

        return view('poz::transaction.product.show', [
            'product'    => $product,
            'tierCount'  => $tierCount,
            'tier1Name'  => $tier1Name,
            'tier2Name'  => $tier2Name,
            'options1'   => $options1,
            'options2'   => $options2,
            'tier1'      => $tier1,
            'tier2'      => $tier2,
            'savedVariants' => $savedVariants,
            'action'     => 'Show'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $productId = $request->product_id;

            $existingRecord = ProductVariant::where('product_id', $productId)->first();
            $oldVariants = $existingRecord ? json_decode($existingRecord->product_variant, true) : [];

            $oldQtyMap = collect($oldVariants)->pluck('qty', 'code')->toArray();

            $newVariants = [];
            $incomingCodes = $request->codes ?? [];

            foreach ($request->tier_1_ids as $index => $t1_id) {
                if (empty($t1_id)) continue;

                $t1_model = TierTransaction::find($t1_id);
                $t2_id = $request->tier_2_ids[$index] ?? null;
                $t2_model = $t2_id ? TierTransaction::find($t2_id) : null;
                $variantName = $t1_model->name . ($t2_model ? ' - ' . $t2_model->name : '');

                $currentCode = $request->codes[$index];
                $newQty = (int)($request->qtys[$index] ?? 0);
                $oldQty = (int)($oldQtyMap[$currentCode] ?? 0);

                if ($newQty != $oldQty) {
                    $diff = $newQty - $oldQty;

                    ProductVariantAdjustment::create([
                        'product_id' => $productId,
                        'code'       => $currentCode,
                        'qty'        => abs($diff),
                        'status'     => $diff > 0 ? 'plus' : 'minus',
                        'created_by' => auth()->id(),
                    ]);
                }

                $newVariants[] = [
                    'tier_1_id' => $t1_id,
                    'tier_2_id' => $t2_id,
                    'name'      => $variantName,
                    'code'      => $currentCode,
                    'price'     => $request->prices[$index] ?? 0,
                    'qty'       => $newQty,
                    'alert_qty' => $request->alert_qtys[$index] ?? 0,
                    'status'    => 'active',
                    'deleted_at' => null
                ];
            }

            foreach ($oldVariants as $old) {
                if (!in_array($old['code'], $incomingCodes)) {

                    if ($old['qty'] > 0 && ($old['status'] ?? 'active') !== 'deleted') {
                        ProductVariantAdjustment::create([
                            'product_id' => $productId,
                            'code'       => $old['code'],
                            'qty'        => $old['qty'],
                            'status'     => 'minus',
                            'created_by' => auth()->id(),
                        ]);
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

            return redirect()->back()->with('msg-sukses', 'Varian dan Adjustment stok berhasil diperbarui.');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('msg-gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
