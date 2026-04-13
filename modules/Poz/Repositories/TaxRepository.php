<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Tax;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait TaxRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'code',
        'name',
        'rate',
        'sale_active',
        'actived_on'
    ];

    /**
     * Handler Notifikasi (Private) - Hanya Create & Update
     */
    private function sendTaxNotification($tax, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;

            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Pajak Diperbarui' : 'Pajak Baru!',
                'message' => "Data pajak <strong>{$tax->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.tax.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-coin-stack',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Tax Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store newly created resource.
     */
    public function storeTax(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['sale_active'] = 0;
            $tax = new Tax(Arr::only($data, $this->keys));

            if ($tax->save()) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $tax->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($tax, $outletId) {
                    $this->sendTaxNotification($tax, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update the current resource.
     */
    public function updateTax(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $tax = Tax::find($id);
            if (!$tax) return false;

            $data['sale_active'] = 0;

            if ($tax->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $tax->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($tax, $outletId) {
                    $this->sendTaxNotification($tax, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
