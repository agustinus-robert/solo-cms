<div class="col-md-4">
    <div class="d-flex flex-column gap-3">
        <div class="p-3 bg-light rounded-3 border">
            <div class="mb-3">
                <label class="form-label small fw-bold">Gambar Produk</label>
                <input type="file" class="form-control form-control-sm" wire:model="form.document">
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label small fw-bold">Barcode Tipe</label>
                <select class="form-select form-select-sm @error('form.barcode') is-invalid @enderror" wire:model="form.barcode">
                    <option value="0">Pilih Barcode</option>
                    <option value="1">CODE128</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Pajak</label>
                <select class="form-select form-select-sm @error('form.tax_rate_id') is-invalid @enderror" wire:model="form.tax_rate_id">
                    <option value="">Tanpa Pajak</option>
                    @foreach ($tax as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Tipe Penjualan</label>
                <select class="form-select form-select-sm" wire:model="form.selling_method">
                    <option value="1">Dengan Stock</option>
                    <option value="2">Dengan Pesanan</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-primary">Tier (Varian)</label>
                <select class="form-select form-select-sm" wire:model.live="form.tier_count">
                    <option value="1">Tier 1</option>
                    <option value="2">Tier 2</option>
                </select>
            </div>

            <div class="p-2 border rounded bg-white shadow-sm">
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Nama Tier 1</label>
                    <select class="form-select form-select-sm" wire:model.live="form.tier_name_1">
                        <option value="">Pilih Tier 1</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(($form['tier_count'] ?? 1) == 2)
                <div class="mb-0">
                    <label class="small fw-bold text-muted">Nama Tier 2</label>
                    <select class="form-select form-select-sm" wire:model.live="form.tier_name_2">
                        <option value="">Pilih Tier 2</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        <div class="p-3 bg-white border rounded-3 shadow-sm">
            <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem;">Visibilitas</h6>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="pos" wire:model="form.is_pos" checked>
                <label class="form-check-label small fw-bold" for="pos">Tampilkan di POS</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ecommerce" wire:model="form.is_ecommerce">
                <label class="form-check-label small fw-bold" for="ecommerce">Tampilkan di E-commerce</label>
            </div>
        </div>
    </div>
</div>
