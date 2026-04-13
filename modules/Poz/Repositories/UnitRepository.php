<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Unit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait UnitRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'code',
        'name'
    ];

    /**
     * Handler Notifikasi (Private)
     */
    private function sendUnitNotification($unit, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;

            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Unit Diperbarui' : 'Unit Baru!',
                'message' => "Unit <strong>{$unit->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.unit.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-layer',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Unit Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store newly created resource.
     */
    public function storeUnit(array $data)
    {
        return DB::transaction(function () use ($data) {
            $unit = new Unit(Arr::only($data, $this->keys));

            if ($unit->save()) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $unit->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($unit, $outletId) {
                    $this->sendUnitNotification($unit, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update the current resource.
     */
    public function updateUnit(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $unit = Unit::find($id);
            if (!$unit) return false;

            if ($unit->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $unit->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($unit, $outletId) {
                    $this->sendUnitNotification($unit, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
