<?php

namespace Modules\Hotel\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Inventory;
use Modules\Hotel\Models\InventoryAdjustment;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    public function show($inventory_adjustmentd)
    {
        $inventory = Inventory::with(['adjustments' => function($query) {
            $query->with('user:id,name');
            $query->latest()->limit(10);
        }])->findOrFail($inventory_adjustmentd);

        return view('hotel::inventory-adjustment.show', compact('inventory'));
    }
    /**
     * Store a new adjustment record.
     * Tidak ada fungsi Update/Delete karena sistem ini bersifat Ledger (Insert Only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:hotel_ref_inventories,id',
            'quantity'     => 'required|integer|min:1',
            'status'       => 'required|in:plus,minus',
            'note'         => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $adjustment = InventoryAdjustment::create([
                'inventory_id' => $request->inventory_id,
                'quantity'     => $request->quantity,
                'status'       => $request->status,
                'note'         => $request->note,
                'user_id'      => auth()->id(),
            ]);

            DB::commit();

           return redirect()->back()
                ->with('success', 'Penambahan stock disesuaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('success', 'Penambahan stock gagal disesuaikan.');
        }
    }

    /**
     * Menampilkan riwayat pergerakan stok untuk satu inventory spesifik
     */
    public function history($inventoryId)
    {
        $history = InventoryAdjustment::where('inventory_id', $inventoryId)
            ->with('inventory')
            ->latest()
            ->paginate(20);

        return response()->json($history);
    }
}
