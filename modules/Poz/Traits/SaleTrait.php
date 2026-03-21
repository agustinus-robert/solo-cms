<?php

namespace Modules\Poz\Traits;

use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Purchase;
use Modules\Poz\Models\Adjustment;
use Modules\Poz\Models\Sale;
use Modules\Poz\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\CashRegister;
use Modules\Poz\Models\SaleDirect;
use Modules\Poz\Models\SaleDirectCart;
use Illuminate\Support\Carbon;

trait SaleTrait
{
    public function getAvailableStock($productId, $outletId, $variantCode = null)
    {
        $today = Carbon::today();

        $baseStock = ProductStock::where('product_id', $productId)
            ->when($variantCode, function ($q) use ($variantCode) {
                return $q->where('variant_code', $variantCode);
            });

        $stockIn = (clone $baseStock)
            ->where('status', 'plus')
            ->whereHasMorph('stockable', [Purchase::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $stockOut = (clone $baseStock)
            ->where('status', 'minus')
            ->whereHasMorph('stockable', [SaleDirect::class, Sale::class, Adjustment::class], function ($query) use ($outletId) {
                $query->whereHas('outlets', function ($q) use ($outletId) {
                    $q->where('outlets.id', $outletId);
                });
            })
            ->sum('qty');

        $qtyInCart = SaleDirectCart::where('product_id', $productId)
            ->when($variantCode, function ($q) use ($variantCode) {
                return $q->where('variant_code', $variantCode);
            })
            ->whereDate('created_at', $today)
            ->whereHas('outlets', function ($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })
            ->sum('qty');

        return (float)($stockIn - $stockOut - $qtyInCart);
    }

    public function calculateSaleTotals($items, $discount = 0)
    {
        $subtotal = 0;

        foreach ($items as $item) {
            if (isset($item['bought_variants']) && is_array($item['bought_variants'])) {
                foreach ($item['bought_variants'] as $v) {
                    $subtotal += (float)($v['qty'] ?? 0) * (float)($v['price'] ?? 0);
                }
            }
        }

        $afterDiscount = max(0, $subtotal - (float)$discount);
        $ppn = $afterDiscount * 0.11;

        $grandTotal = $afterDiscount + $ppn;

        return [
            'sub_total'   => (float)$subtotal,
            'discount'    => (float)$discount,
            'ppn'         => (float)$ppn,
            'grand_total' => (float)$grandTotal
        ];
    }

   public function createSaleTransaction($request, $totals)
    {
        $grandTotal = (float)$totals['grand_total'];
        $paidAmount = (float)($request->paid_amount ?? $grandTotal);
        $change = $this->calculateChange($paidAmount, $grandTotal);

        $sale = Sale::create([
            'reference'   => $totals['reference'] ?? 'SALE-' . strtoupper(uniqid()),
            'customer_id' => $request->customer_id ?? null,
            'sale_status' => 3,
            'discount'    => $request->discount ?? 0,
            'pos'         => 1,
            'sub_total'   => $totals['sub_total'] ?? $grandTotal,
            'grand_total' => $grandTotal,
            'paid_amount' => $paidAmount,
            'change'      => $change,
        ]);

        $sale->outlets()->sync([$request->outlet_id]);

        if ($change > 0) {
            $this->handleCashChange($change, $sale->reference, $request->outlet_id);
        }

        return $sale;
    }

    public function handleCashChange($changeAmount, $reference, $outletId)
    {
        $register = CashRegister::where('status', 'open')
            ->where('casier_id', Auth::id())
            ->whereHas('outlets', function($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })->first();

        if ($register) {
            $register->decrement('money', $changeAmount);

            $register->logCash()->create([
                'status'   => 'minus',
                'money'    => $changeAmount,
                'log_type' => 'transaction',
                'reason'   => "Kembalian Penjualan #" . $reference,
            ]);
        }
    }

    public function deductStock($items, $outletId, $saleId)
    {
        foreach ($items as $item) {
            if (!isset($item['bought_variants'])) continue;

            foreach ($item['bought_variants'] as $v) {
                $variantCode = $v['code'];
                $qtyToReduce = (int)$v['qty'];

                $newStock = ProductStock::create([
                    'product_id'     => $item['id'],
                    'variant_code'   => $variantCode,
                    'qty'            => $qtyToReduce,
                    'status'         => 'minus',
                    'stockable_type' => \Modules\Poz\Models\Sale::class,
                    'stockable_id'   => $saleId,
                ]);

                $newStock->outlets()->sync([$outletId]);
            }
        }
    }

    public function storeSaleItems($sale, $items)
    {
        foreach ($items as $item) {
            if (!isset($item['bought_variants'])) continue;

            foreach ($item['bought_variants'] as $v) {
                $sale->saleItems()->create([
                    'product_id'   => $item['id'],
                    'variant_code' => $v['code'],
                    'qty'          => $v['qty'],
                    'price'        => $v['price'] ?? 0,
                ]);
            }
        }
    }

    public function calculateChange($paidAmount, $grandTotal)
    {
        $paid = (float) $paidAmount;
        $total = (float) $grandTotal;

        return ($paid > $total) ? ($paid - $total) : 0;
    }
}
