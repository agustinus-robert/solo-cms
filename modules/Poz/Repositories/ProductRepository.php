<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Purchase;
use Modules\Poz\Models\Casier;
use Modules\Poz\Models\SupplierSchedule;
use Modules\Poz\Models\PurchaseItems;
use Modules\Poz\Models\ProductStock;

trait ProductRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'type',
        'alert_qty',
        'code',
        'slug',
        'name',
        'barcode',
        'brand_id',
        'category_id',
        'sub_category_id',
        'unit_id',
        'tax_rate_id',
        'price',
        'wholesale',
        'location',
        'description',
        'image_name',
        'weight'
    ];

    /**
     * Store newly created resource.
     */
    public function storeProduct(array $data, array $sch = [])
    {
        $location = 'file_product/' . uniqid();

        if (isset($data['document']) && $data['document'] instanceof \Illuminate\Http\UploadedFile) {
            $data['document']->storeAs($location, $data['document']->getFilename(), 'public');
            $data['location'] = $location;
            $data['image_name'] = $data['document']->getFilename();
        }

        $data['slug'] = Str::slug($data['name']);
        $product = new Product(Arr::only($data, $this->keys));
        if ($product->save()) {
            $outletId = $data['outlet'];

            if ($outletId) {
                $product->outlets()->attach($outletId);
            }

            if(count($sch) > 0){
                $purchase = Purchase::create([
                    'reference' => 'REF' . '-' . rand(),
                    'supplier_id' => $sch['supplier'],
                    'is_pos' => 1,
                    'purchase_status' => 3,
                    'purchase_date' => now(),
                    'grand_total' => ($data['wholesale'] * $sch['qty']),
                    'discount' => 0,
                    'created_by' => auth()->user()->id
                ]);

                $casier = Casier::where('user_id', auth()->user()->id)->first();
                $purchase->outlets()->syncWithoutDetaching($casier->outlet_id);

                $purchaseItems = new PurchaseItems();
                $purchaseItems->purchase_id = $purchase->id;
                $purchaseItems->product_id = $product->id;
                $purchaseItems->qty = $sch['qty'];
                $purchaseItems->created_by = $casier->user_id;
                $purchaseItems->save();

                SupplierSchedule::create([
                    'supplier_id' => $sch['supplier'],
                    'product_id' => $product->id,
                    'day'        => null,
                    'time'       => $sch['shifts'],
                ]);

                $productStock = ProductStock::create([
                    'product_id' => $product->id,
                    'supplier_id' => $sch['supplier'],
                    'stockable_id' => $purchase->id,
                    'stockable_type' => \Modules\Poz\Models\Purchase::class,
                    'status' => 'plus',
                    'grand_total' => ($data['wholesale'] * $sch['qty']),
                    'wholesale' => $data['wholesale'],
                    'qty' => $sch['qty'],
                    'created_by' => $casier->user_id,
                ]);

                $productStock->outlets()->syncWithoutDetaching($casier->outlet_id);
            }

            $this->saveMetas($product, $data);

            return true;
        }

        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateProduct(array $data, $id)
    {
        $location = 'file_product/' . uniqid();

        if (isset($data['document']) && $data['document'] instanceof \Illuminate\Http\UploadedFile) {
            $data['document']->storeAs($location, $data['document']->getFilename(), 'public');
            $data['location'] = $location;
            $data['image_name'] = $data['document']->getFilename();
        }

        $product = Product::find($id);
        $data['slug'] = Str::slug($data['name']);

        if ($product->update(Arr::only($data, $this->keys))) {
            $outletId = $data['outlet'];

            if ($outletId) {
                $product->outlets()->attach($outletId);
            }

            $product->metas()->delete();
            $this->saveMetas($product, $data);

            return true;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyProduct($id)
    {
        if (Product::where('id', $id)->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreProduct($id)
    {
        if (Product::onlyTrashed()->find($id)->restore()) {
            return true;
        }
        return false;
    }

    private function saveMetas($product, $data)
    {
        $metaKeys = [
            'selling_method',
            'tier_count',
            'tier_name_1',
            'tier_name_2',
            'is_pos',
            'is_ecommerce'
        ];

        foreach ($metaKeys as $key) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if (is_bool($value)) $value = $value ? '1' : '0';

                $product->metas()->create([
                    'meta_key'   => $key,
                    'meta_value' => $value
                ]);
            }
        }
    }
}
