<?php

namespace Modules\Hotel\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Inventory;
use Modules\Hotel\Http\Requests\Inventory\StoreRequest;
use Modules\Hotel\Http\Requests\Inventory\UpdateRequest;

use Modules\Hotel\Repositories\Master\InventoryRepositories;

class InventoryController extends Controller
{
    use InventoryRepositories;

    public function index(Request $request)
    {
        $inventories = Inventory::with('adjustments')->latest()->get();

        if ($request->ajax()) {
            return view('hotel::inventory._table', compact('inventories'))->render();
        }

        return view('hotel::inventory.index', compact('inventories'));
    }

    public function create()
    {
        $inventory = null;
        return view('hotel::inventory.upsert', compact('inventory'));
    }

    public function store(StoreRequest $request)
    {
        $this->upsertInventory($request->transform());
        return redirect()->route('hotel::inventory.index')->with('success', 'Barang inventaris berhasil disimpan.');
    }

    public function edit(Inventory $inventory)
    {
        return view('hotel::inventory.upsert', compact('inventory'));
    }

    public function update(UpdateRequest $request, Inventory $inventory)
    {
        $this->upsertInventory($request->transform(), $inventory->id);
        return redirect()->route('hotel::inventory.index')->with('success', 'Barang inventaris berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $this->deleteInventory($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
