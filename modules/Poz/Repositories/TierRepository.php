<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Tier;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait TierRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name',
        'type'
    ];

    /**
     * Handler Notifikasi (Private) - Hanya Create & Update
     */
    private function sendTierNotification($tier, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;

            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Tier Harga Diperbarui' : 'Tier Harga Baru!',
                'message' => "Tier <strong>{$tier->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.tier.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-layer-plus',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Tier Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store newly created resource.
     */
    public function storeTier(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tier = new Tier(Arr::only($data, $this->keys));

            if ($tier->save()) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $tier->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($tier, $outletId) {
                    $this->sendTierNotification($tier, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update the current resource.
     */
    public function updateTier(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $tier = Tier::find($id);
            if (!$tier) return false;

            if ($tier->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $tier->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($tier, $outletId) {
                    $this->sendTierNotification($tier, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
