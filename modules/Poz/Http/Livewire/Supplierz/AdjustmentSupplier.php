<?php

namespace Modules\Poz\Http\Livewire\Supplierz;

use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Supplier;
use Modules\Poz\Models\SupplierSchedule;
use Modules\Poz\Models\Adjustment as Adj;
use Modules\Poz\Repositories\AdjustmentRepository;
use Modules\Poz\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdjustmentSupplier extends Component
{
    use WithFileUploads, AdjustmentRepository;

    public $form = [];
    public $action;
    public $outletNow = '';
    public $outlets = [];

    /** * Menggunakan inisialisasi null/collection untuk menghindari error Intelephense P1006
     */
    public $products;
    public $shift;

    public function mount($action)
    {
        $this->action = $action;
        $this->outlets = Outlet::whereNull('deleted_at')->get();

        $this->products = collect();
        $this->shift = collect();
        $this->form['status'] = '';
        $this->form['product_id'] = '';
        $this->form['shift'] = '';
        $this->form['qty'] = 0;
    }

    public function showProduct($outletId)
    {
        if (empty($outletId)) {
            $this->outletNow = null;
            $this->products = collect();
            $this->shift = collect();
            return;
        }

        $this->outletNow = $outletId;

        $this->products = Product::whereHas('schedule', function ($query) use ($outletId) {
            $query->whereHas('supplier', function ($q) use ($outletId) {
                $q->where('user_id', Auth::id())
                  ->whereHas('outlets', fn($oq) => $oq->where('outlets.id', $outletId));
            });
        })->get();

        if ($this->products->isEmpty()) {
            $this->shift = collect();
            session()->flash('msg-gagal', 'Anda belum memiliki jadwal (schedule) di outlet ini.');
        } else {
            $this->shift = collect();
            $this->form['product_id'] = '';
        }
    }

    public function showShift($productId)
    {
        if (empty($productId)) {
            $this->shift = collect();
            return;
        }

        $outletId = $this->outletNow;

        $this->shift = SupplierSchedule::where('product_id', $productId)
            ->whereHas('supplier', function ($query) use ($outletId) {
                $query->where('user_id', Auth::id())
                    ->whereHas('outlets', fn($q) => $q->where('outlets.id', $outletId));
            })
            ->get();

        if ($this->shift->isEmpty()) {
            session()->flash('msg-gagal', 'Shift tidak ditemukan untuk produk terpilih.');
        }
    }

    public function save()
    {
        $this->validate([
            'form.outlet_id'  => 'required',
            'form.product_id' => 'required',
            'form.status'     => 'required',
            'form.qty'        => 'required|numeric|min:1',
            'form.shift'      => 'required',
        ], [
            'form.outlet_id.required'  => 'Pilih outlet',
            'form.product_id.required' => 'Pilih produk',
            'form.status.required'     => 'Pilih status plus/minus',
            'form.qty.min'             => 'Qty minimal 1',
        ]);

        $supplier = Supplier::where('user_id', Auth::id())->first();

        if (!$supplier) {
            session()->flash('msg-gagal', "Data supplier tidak ditemukan.");
            return;
        }

        $this->form['supplier_id'] = $supplier->id;
        $this->form['is_supplier'] = 1;

        if ($this->storeAdjustment($this->form, $this->outletNow)) {
            return redirect(route('poz::supplierz.adjustment.index'))->with('msg-sukses', "Adjustment berhasil disimpan");
        }

        session()->flash('msg-gagal', "Gagal menyimpan data ke database.");
    }

    public function render()
    {
        return view('poz::livewire.supplierz.adjustment');
    }
}
