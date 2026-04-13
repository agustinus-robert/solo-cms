<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait CategoryRepository
{
    private $keys = [
        'code', 'name', 'slug', 'description', 'parent_id', 'location', 'image_name'
    ];

    /**
     * Handler Upload & Slug (Private)
     */
    private function handleCategoryUpload(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $location = 'file_category/' . uniqid();
            $filename = $data['image']->getClientOriginalName();
            $data['image']->storeAs($location, $filename, 'public');

            $data['location'] = $location;
            $data['image_name'] = $filename;
        } elseif (!isset($data['location'])) {
            $data['location'] = 'dummy/';
            $data['image_name'] = 'no-pictures.png';
        }

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['parent_id'] = (empty($data['parent_id']) ? null : $data['parent_id']);

        return $data;
    }

    /**
     * Handler Notifikasi (Private)
     */
    private function sendCategoryNotification($category, $type = 'create', $outletId = null)
    {
        try {
            if (!$outletId) return;

            $isUpdate = ($type === 'update');

            auth()->user()->broadcastToSameOutlet([
                'title'   => $isUpdate ? 'Kategori Diperbarui' : 'Kategori Baru!',
                'message' => "Kategori <strong>{$category->name}</strong> telah " . ($isUpdate ? 'diperbarui' : 'ditambahkan') . " oleh <strong>" . auth()->user()->name . "</strong>.",
                'link'    => route('poz::master.category.index') . '?outlet=' . $outletId,
                'icon'    => $isUpdate ? 'bx bx-edit-alt' : 'bx bx-category',
                'color'   => $isUpdate ? 'warning' : 'success',
            ], $outletId);
        } catch (\Exception $e) {
            Log::error("Realtime Category Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Store Category
     */
    public function storeCategory(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data = $this->handleCategoryUpload($data);
            $category = new Category(Arr::only($data, $this->keys));

            if ($category->save()) {
                $outletId = $data['outlet'] ?? null;
                if ($outletId) {
                    $category->outlets()->attach($outletId);
                }

                DB::afterCommit(function () use ($category, $outletId) {
                    $this->sendCategoryNotification($category, 'create', $outletId);
                });

                return true;
            }
            return false;
        });
    }

    /**
     * Update Category
     */
    public function updateCategory(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $category = Category::find($id);
            if (!$category) return false;

            $data = $this->handleCategoryUpload($data);

            if ($category->update(Arr::only($data, $this->keys))) {
                $outletId = $data['outlet'] ?? null;
                if ($outletId) {
                    $category->outlets()->syncWithoutDetaching($outletId);
                }

                DB::afterCommit(function () use ($category, $outletId) {
                    $this->sendCategoryNotification($category, 'update', $outletId);
                });

                return true;
            }
            return false;
        });
    }
}
