<?php

namespace Modules\Hotel\Repositories\Master;

use Modules\Hotel\Models\Inventory;

trait InventoryRepositories
{
    public function upsertInventory(array $data, ?int $id = null): Inventory
    {
        return Inventory::updateOrCreate(
            ['id' => $id],
            [
                'name'        => $data['name'],
                'type'        => $data['type'],
                'unit'        => $data['unit'],
                'min_stock'   => $data['min_stock'],
                'description' => $data['description'] ?? null,
            ]
        );
    }

    public function deleteInventory(int $id): bool
    {
        return Inventory::findOrFail($id)->delete();
    }
}
