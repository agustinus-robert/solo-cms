<?php

namespace Modules\Hotel\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\Inventory;

class RoomInventoryController extends Controller
{
    public function show(Room $room_inventory)
    {
        $allInventories = Inventory::orderBy('name', 'asc')->get();
        $room_inventory->load('inventories');

        $room = $room_inventory;

        return view('hotel::room-inventory.index', compact('room', 'allInventories'));
    }

    public function upsert(Room $room)
    {
        $allInventories = Inventory::orderBy('name', 'asc')->get();
        $room->load('inventories');

        return view('hotel::room-inventory.upsert', compact('room', 'allInventories'));
    }

    public function update(Request $request, Room $room_inventory)
    {
     $request->validate([
        'inventory_ids' => 'nullable|array',
        'quantities'    => 'nullable|array',
        'notes'         => 'nullable|array',
    ]);
        try {
            \DB::beginTransaction();

            $oldInventories = $room_inventory->inventories;
            foreach ($oldInventories as $oldItem) {
                $oldItem->increment('total_stock', $oldItem->pivot->quantity);
            }

            $items = [];

            $selectedIds = $request->input('inventory_ids', []);

            foreach ($selectedIds as $id) {
                $qty = (int) ($request->quantities[$id] ?? 0);

                if ($qty > 0) {
                    $inventory = \Modules\Hotel\Models\Inventory::find($id);

                    if (!$inventory) continue;

                    if ($inventory->total_stock < $qty) {
                        throw new \Exception("Stok {$inventory->name} tidak mencukupi!");
                    }

                    $items[$id] = [
                        'quantity' => $qty,
                        'note'     => $request->notes[$id] ?? null
                    ];

                    $inventory->decrement('total_stock', $qty);
                }
            }

            $room_inventory->inventories()->sync($items);

            \DB::commit();

            return redirect()
                ->route('hotel::room-inventory.show', $room_inventory->id)
                ->with('success', 'Inventaris kamar ' . $room_inventory->room_number . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            dd($e->getMessage());
            \DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
