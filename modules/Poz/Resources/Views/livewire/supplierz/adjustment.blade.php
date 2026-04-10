<div>
    <div class="row">
        <div class="col-12">
            @if (session()->has('msg-sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-2"></i> {{ session('msg-sukses') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('msg-gagal'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-2"></i> {{ session('msg-gagal') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="font-size-18 mb-0">Adjustment Stok</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Adjustment</a></li>
                        <li class="breadcrumb-item active">{{ $action }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="card-title mb-0">{{ $action }} Adjustment</h5>
        </div>
        <form wire:submit.prevent="save">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Outlet</label>
                            <select class="form-select" wire:change="showProduct($event.target.value)" wire:model="form.outlet_id">
                                <option value="">-- Pilih Outlet --</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            @error('form.outlet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Product</label>
                            <select class="form-select" wire:model="form.product_id" wire:change="showShift($event.target.value)" {{ $products->isEmpty() ? 'disabled' : '' }}>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('form.product_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Shift</label>
                            <select class="form-select" wire:model="form.shift" {{ $shift->isEmpty() ? 'disabled' : '' }}>
                                <option value="">-- Pilih Shift --</option>
                                @if($shift->isNotEmpty())
                                    @php $timeMap = ['morning' => 1, 'afternoon' => 2, 'evening' => 3]; @endphp
                                    @foreach($shift as $val)
                                        <option value="{{ $timeMap[$val->time] ?? '' }}">{{ ucfirst($val->time) }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('form.shift') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Status Adjustment</label>
                            <select class="form-select" wire:model="form.status">
                                <option value="">-- Pilih Status --</option>
                                <option value="plus">Plus (Tambah Stok)</option>
                                <option value="minus">Minus (Kurangi Stok)</option>
                            </select>
                            @error('form.status') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Quantity (Qty)</label>
                            <input class="form-control" type="number" wire:model="form.qty" min="1" placeholder="Masukkan jumlah stok" />
                            @error('form.qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Catatan / Alasan</label>
                            <textarea class="form-control" wire:model="form.note" rows="3" placeholder="Contoh: Barang rusak atau stok sisa pameran..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top text-end">
                <button type="submit" class="btn btn-primary px-5 shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan Adjustment</span>
                    <span wire:loading><i class="bx bx-loader bx-spin me-2"></i> Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>
