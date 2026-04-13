<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Supplier;
use Illuminate\Support\Str;
use Modules\Account\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait SupplierRepository
{
    private $keys = [
        'code', 'name', 'email', 'phone', 'address', 'location', 'image_name'
    ];

    /**
     * Handler Upload (Private)
     */
    private function handleSupplierUpload(array $data, $document)
    {
        if (isset($document) && $document instanceof \Illuminate\Http\UploadedFile) {
            $location = 'file_supplier/' . uniqid();
            $fileName = $document->getClientOriginalName();
            $document->storeAs($location, $fileName, 'public');

            $data['location'] = $location;
            $data['image_name'] = $fileName;
        }

        return $data;
    }

    /**
     * Handler Notifikasi (Private) - Create & Update Only
     */
    private function sendSupplierNotification($supplier, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;

            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Supplier Diperbarui' : 'Supplier Baru!',
                'message' => "Supplier <strong>{$supplier->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.supplier.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-store-alt',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Supplier Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store Supplier
     */
    public function storeSupplier(array $data, $document)
    {
        return DB::transaction(function () use ($data, $document) {
            $data = $this->handleSupplierUpload($data, $document);
            $supplier = new Supplier(Arr::only($data, $this->keys));

            if ($supplier->save()) {
                // Logika pembuatan user dummy supplier
                $user = new User([
                    'name' => $data['name'],
                    'username' => Str::slug($data['name']) . rand(100, 999),
                    'email' => $data['email'],
                    'password' => bcrypt('password'), // Sebaiknya di-hash
                    'current_team_id' => 1
                ]);
                $user->save();

                $outletId = $data['outlet'] ?? null;
                if ($outletId) {
                    $supplier->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($supplier, $outletId) {
                    $this->sendSupplierNotification($supplier, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update Supplier
     */
    public function updateSupplier(array $data, $id, $document)
    {
        return DB::transaction(function () use ($data, $id, $document) {
            $supplier = Supplier::find($id);
            if (!$supplier) return false;

            $data = $this->handleSupplierUpload($data, $document);

            if ($supplier->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $supplier->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($supplier, $outletId) {
                    $this->sendSupplierNotification($supplier, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
