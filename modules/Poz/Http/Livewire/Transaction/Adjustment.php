<?php

namespace Modules\Poz\Http\Livewire\Transaction;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Supplier;
use Modules\Poz\Models\SupplierSchedule;
use Modules\Poz\Models\Adjustment as Adj;
use Modules\Poz\Models\ProductVariant;
use Modules\Poz\Repositories\AdjustmentRepository;
use Livewire\Attributes\On;
use DB;

class Adjustment extends Component
{
    use WithFileUploads, AdjustmentRepository;

    public $form = [];
    public $action;
    public $products = [];
    public $supplier = [];
    public $categories = '';
    public $brand = '';
    public $supplierNow = '';
    public $shift = [];
    public $variants = [];

    // Properti baru untuk kontrol jadwal
    public $is_schedule = 'false'; // Diubah dari 'true' ke 'false' agar defaultnya tanpa jadwal

    public function mount($action, Request $req)
    {
        $this->form['outlet'] = request()->query('outlet', auth()->user()->current_outlet_id);
        $this->action = $action;
        $this->products = []; // Mulai kosong sebelum supplier dipilih
        $this->supplier = Supplier::whereNull('deleted_at')->get();
    }

    // Trigger otomatis saat radio button berubah
    public function updatedIsSchedule()
    {
        if (!empty($this->form['supplier_id'])) {
            $this->showProduct($this->form['supplier_id']);
        }
    }

    public function showProduct($supplierId){
        if (empty($supplierId)) {
            $this->supplierNow = null;
            $this->products = collect();
            $this->shift = collect();
            $this->variants = [];
            return;
        }

        $this->supplierNow = $supplierId;
        $outNow = $this->form['outlet'];

        if ($this->is_schedule === 'true') {
            $this->products = Product::whereHas('schedule', function ($query) use ($supplierId, $outNow) {
                $query->whereHas('supplier', function ($q) use ($supplierId, $outNow) {
                    $q->where('ref_suppliers.id', $supplierId)
                    ->whereHas('outlets', fn($oq) => $oq->where('outlets.id', $outNow));
                });
            })->whereNull('deleted_at')->get();
        } else {
            $this->products = Product::whereNull('deleted_at')->get();
        }
    }

    public function showShift($productId){
        if (empty($productId)) {
            $this->shift = collect();
            $this->variants = [];
            $this->form['variant_code'] = null;
            return;
        }

        $suppNow = $this->supplierNow;
        $outNow = $this->form['outlet'];

        $variantRecord = ProductVariant::where('product_id', $productId)->first();

        if ($variantRecord) {
            $allVariants = json_decode($variantRecord->product_variant, true);
            $activeVariants = collect($allVariants)->where('status', 'active');

            $this->variants = $activeVariants->toArray();

            $firstVariant = $activeVariants->first();
            if ($activeVariants->count() === 1 && ($firstVariant['variant_type'] ?? '') === 'no_variant') {
                $this->form['variant_code'] = $firstVariant['code'];
            } else {
                $this->form['variant_code'] = null;
            }
        } else {
            $this->variants = [];
            $this->form['variant_code'] = null;
        }

        $this->shift = SupplierSchedule::where('product_id', $productId)
        ->whereHas('supplier', function ($query) use ($suppNow, $outNow) {
            $query->where('ref_suppliers.id', $suppNow)
                ->whereHas('outlets', fn($q) => $q->where('outlets.id', $outNow));
        })
        ->get();
    }

    public function save(){
        if ($this->storeAdjustment($this->form, $this->form['outlet']) == true) {
            return redirect(route('poz::transaction.adjustment.index') . '?outlet=' . $this->form['outlet'])->with('msg-sukses', "Data berhasil disimpan");
        } else {
            return redirect(route('poz::transaction.adjustment.index') . '?outlet=' . $this->form['outlet'])->with('msg-gagal', "Data gagal disimpan");
        }
    }

    public function modalClosed()
    {
        $this->reset(['form', 'products', 'shift', 'variants', 'supplierNow']);
    }

    public function render()
    {
        $data['productInfo'] = $this->products;
        return view('poz::livewire.transaction.adjustment', $data);
    }
}
