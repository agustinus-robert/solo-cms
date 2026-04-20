<?php

namespace Modules\Hotel\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\Inventory;
use Illuminate\Support\Facades\DB;

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
            DB::beginTransaction();

            $oldInventories = $room_inventory->inventories;
            foreach ($oldInventories as $oldItem) {
                $oldItem->adjustments()->create([
                    'quantity' => $oldItem->pivot->quantity,
                    'status'   => 'plus',
                    'note'     => "System: Reversal inventaris Kamar {$room_inventory->room_number} untuk update data",
                    'user_id'  => auth()->id(),
                ]);
            }

            $items = [];
            $selectedIds = $request->input('inventory_ids', []);

            foreach ($selectedIds as $id) {
                $qty = (int) ($request->quantities[$id] ?? 0);

                if ($qty > 0) {
                    $inventory = Inventory::where('id', $id)->lockForUpdate()->first();

                    if (!$inventory) continue;
                    if ($inventory->current_stock < $qty) {
                        throw new \Exception("Stok {$inventory->name} tidak mencukupi! Sisa stok: {$inventory->current_stock}");
                    }

                    $items[$id] = [
                        'quantity' => $qty,
                        'note'     => $request->notes[$id] ?? null
                    ];

                    $inventory->adjustments()->create([
                        'quantity' => $qty,
                        'status'   => 'minus',
                        'note'     => "Alokasi Kamar {$room_inventory->room_number}: " . ($request->notes[$id] ?? 'Standard Setup'),
                        'user_id'  => auth()->id(),
                    ]);
                }
            }

            $room_inventory->inventories()->sync($items);

            DB::commit();

            return redirect()
                ->route('hotel::room-inventory.show', $room_inventory->id)
                ->with('success', "Inventaris kamar {$room_inventory->room_number} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
