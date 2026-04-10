<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\HRMS\Models\Employee;
use Modules\Poz\Models\ProductQuotation;
use Modules\Poz\Models\ProductQuotationItems;
use App\Notifications\GlobalGenericNotification;
use Modules\Account\Models\User;
use Illuminate\Support\Str;

trait QuotationRepository
{
    /**
     * Define the form keys for resource
     */
    private $keyReference = [
        'reference',
        'payment_on',
        'status'
    ];

    private $keysItems = [
        'product_quotation_id',
        'name',
        'status',
        'price',
        'location',
        'image_name'
    ];

    private function sendQuotationNotifications($quotation, $outletId, $type = 'store')
    {
        $supplierName = Auth::user()->name;
        $isStore = $type === 'store';

        $payload = [
            'title'   => $isStore ? 'Penawaran Baru Masuk' : 'Penawaran Diperbarui',
            'message' => $isStore
                ? "Supplier <strong>{$supplierName}</strong> mengirimkan penawaran baru: <strong>{$quotation->reference}</strong>"
                : "Supplier <strong>{$supplierName}</strong> memperbarui detail penawaran: <strong>{$quotation->reference}</strong>",
            'link'    => route('poz::supplierz.quotation.index'),
            'icon'    => $isStore ? 'bx bx-file-blank' : 'bx bx-revision',
            'color'   => $isStore ? 'primary' : 'info'
        ];

        $managementUsers = User::role(['administrator'])->get();

        foreach ($managementUsers as $user) {
            $user->notify(new GlobalGenericNotification($payload));
        }

        if ($outletId) {
            $outletEmployees = Employee::whereHas('outlets', function($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            })->with('user')->get();

            foreach ($outletEmployees as $employee) {
                if ($employee->user) {
                    $employee->user->notify(new GlobalGenericNotification($payload));
                }
            }
        }
    }

    /**
     * Store newly created resource.
     */
    public function storeQuotation(array $data, array $rows, $outletId)
    {
        DB::beginTransaction();

        try {
            $data['reference'] = 'INV/QUOTE/' . strtoupper(uniqid());
            $data['status'] = 1;
            $productInv = new ProductQuotation(Arr::only($data, $this->keyReference));

            if (! $productInv->save()) {
                throw new \Exception('Gagal menyimpan quotation utama');
            }

            if ($outletId) {
                $productInv->outlets()->attach($outletId);
            }

            foreach ($rows as $row) {
                $location = 'file_supplier/' . uniqid();

                if (isset($row['file']) && $row['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = $row['file']->getClientOriginalName();
                    $row['file']->storeAs($location, $filename, 'public');

                    $row['location']   = $location;
                    $row['image_name'] = $filename;
                }

                $productQuotation = new ProductQuotationItems();
                $productQuotation->product_quotation_id = $productInv->id;
                $productQuotation->name = $row['name'];
                $productQuotation->price = $row['price'];
                $productQuotation->status = null;
                $productQuotation->location = $row['location'] ?? null;
                $productQuotation->image_name = $row['image_name'] ?? null;
                $productQuotation->save();
            }

            $notifData = [
                'title'   => 'Penawaran Baru Masuk',
                'message' => "Supplier <strong>" . Auth::user()->name . "</strong> mengirimkan penawaran baru dengan referensi: <strong>{$productInv->reference}</strong>",
                'link'    => route('poz::supplierz.quotation.index'),
                'icon'    => 'bx bx-file-blank',
                'color'   => 'primary'
            ];

            $this->sendQuotationNotifications($productInv, $outletId, 'store');

            DB::commit();
            session()->flash('msg-sukses', 'Quotation berhasil disimpan!');
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            session()->flash('msg-gagal', 'Terjadi kesalahan saat menyimpan quotation: ' . $e->getMessage());
            return false;
        }
    }

    public function updateQuotation(array $data, array $rows, $quo, $outletId)
    {
        DB::beginTransaction();

        try {
            $quotation = ProductQuotation::find($quo);
            $quotation->update(Arr::only($data, $this->keyReference));

            if ($outletId) {
                $quotation->outlets()->sync($outletId);
            }

            $quotation->productQuotationItems()->delete();

            foreach ($rows as $row) {
                $location = $row['location'] ?? 'file_supplier/' . uniqid();

                if (isset($row['file']) && $row['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = $row['file']->getClientOriginalName();
                    $row['file']->storeAs($location, $filename, 'public');

                    $row['location'] = $location;
                    $row['image_name'] = $filename;
                }

                $productQuotation = new ProductQuotationItems();
                $productQuotation->product_quotation_id = $quotation->id;
                $productQuotation->name = $row['name'];
                $productQuotation->price = $row['price'];
                $productQuotation->status = $row['status'] ?? 1;
                $productQuotation->location = $row['location'] ?? null;
                $productQuotation->image_name = $row['image_name'] ?? null;
                $productQuotation->save();
            }

            $notifUpdate = [
                'title'   => 'Penawaran Diperbarui',
                'message' => "Supplier <strong>" . Auth::user()->name . "</strong> memperbarui detail penawaran <strong>{$quotation->reference}</strong>",
                'link'    => route('poz::supplierz.quotation.index'),
                'icon'    => 'bx bx-revision',
                'color'   => 'info'
            ];

            $this->sendQuotationNotifications($quotation, $outletId, 'update');

            DB::commit();
            session()->flash('msg-sukses', 'Quotation berhasil diperbarui!');
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('msg-gagal', 'Terjadi kesalahan saat memperbarui quotation: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * Remove the current resource.
     */
    public function destroyInquiry($id)
    {
        if (Brand::where('id', $id)->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreInquiry($id)
    {
        if (Brand::onlyTrashed()->find($id)->restore()) {
            return true;
        }
        return false;
    }
}
