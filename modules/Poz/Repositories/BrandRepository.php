<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait BrandRepository
{
    private $keys = [
        'code', 'name', 'slug', 'description', 'location', 'image_name'
    ];

    /**
     * Handler Upload & Slug
     */
    private function handleUpload(array $data)
    {
        if (isset($data['document']) && $data['document'] instanceof \Illuminate\Http\UploadedFile) {
            $location = 'file_brand/' . uniqid();
            $filename = $data['document']->getClientOriginalName();
            $data['document']->storeAs($location, $filename, 'public');

            $data['location'] = $location;
            $data['image_name'] = $filename;
        }

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $data;
    }

    /**
     * Handler Notifikasi
     */
    private function sendBrandNotification($brand, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;
            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Brand Diperbarui' : 'Brand Baru!',
                'message' => "Brand <strong>{$brand->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.brand.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-tag',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Brand Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store dengan Transaction & AfterCommit
     */
    public function storeBrand(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data = $this->handleUpload($data);
            $brand = new Brand(Arr::only($data, $this->keys));

            if ($brand->save()) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $brand->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($brand, $outletId) {
                    $this->sendBrandNotification($brand, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update dengan Transaction & AfterCommit
     */
    public function updateBrand(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $brand = Brand::find($id);
            if (!$brand) return false;

            $data = $this->handleUpload($data);

            if ($brand->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;

                if ($outletId) {
                    $brand->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($brand, $outletId) {
                    $this->sendBrandNotification($brand, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
