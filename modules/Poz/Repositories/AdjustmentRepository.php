<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Adjustment;
use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Product;
use Modules\Account\Models\User;
use Modules\HRMS\Models\Employee;
use App\Notifications\GlobalGenericNotification;
use Illuminate\Support\Str;

trait AdjustmentRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'product_id',
        'supplier_id',
        'status', // plus / minus
        'qty',
        'shift',
        'product_status'
    ];

    /**
     * Store newly created resource.
     */
    public function storeAdjustment(array $data, $outletId)
    {
        return DB::transaction(function () use ($data, $outletId) {
            $adjustment = new Adjustment(Arr::only($data, $this->keys));

            if ($adjustment->save()) {
                if ($outletId) {
                    $adjustment->outlets()->attach($outletId);

                    $shiftMap = [1 => 'morning', 2 => 'afternoon', 3 => 'evening'];
                    $product = Product::find($data['product_id']);
                    $isNotStock = isset($data['supplier_id']) ? 1 : null;

                    $finalQty = ($data['status'] === 'minus') ? -abs($data['qty']) : abs($data['qty']);

                    $productStock = ProductStock::create([
                        'product_id'     => $data['product_id'],
                        'supplier_id'    => $data['supplier_id'] ?? null,
                        'stockable_id'   => $adjustment->id,
                        'stockable_type' => Adjustment::class,
                        'variant_code'   => $data['variant_code'] ?? null,
                        'status'         => $data['status'],
                        'grand_total'    => ($product->wholesale * $finalQty),
                        'wholesale'      => $product->wholesale,
                        'qty'            => $finalQty,
                        'shift'          => $shiftMap[$data['shift'] ?? ''] ?? null,
                        'created_by'     => auth()->id(),
                        'is_not_stock'   => $isNotStock
                    ]);

                    $productStock->outlets()->syncWithoutDetaching($outletId);

                    DB::afterCommit(function () use ($adjustment, $product, $outletId) {
                        $this->sendAdjustmentNotifications($adjustment, $product, $outletId);
                    });
                }
                return true;
            }
            return false;
        });
    }

    /**
     * Handler Notifikasi Adjustment (Private)
     */
    private function sendAdjustmentNotifications($adjustment, $product, $outletId)
    {
        try {
            $statusText = $adjustment->status === 'plus' ? 'Penambahan' : 'Pengurangan';

            \Modules\Account\Models\User::broadcastSystemNotification([
                'title'   => 'Adjustment Stok Baru',
                'message' => "<strong>{$statusText}</strong> stok pada produk <strong>{$product->name}</strong> sebanyak <strong>" . abs($adjustment->qty) . "</strong>.",
                'link'    => route('poz::supplierz.adjustment.index') . '?outlet=' . $outletId,
                'icon'    => $adjustment->status === 'plus' ? 'bx bx-trending-up' : 'bx bx-trending-down',
                'color'   => $adjustment->status === 'plus' ? 'success' : 'danger',
            ]);

        } catch (\Exception $e) {
            \Log::error("Global Broadcast Error: " . $e->getMessage());
        }
    }
}
