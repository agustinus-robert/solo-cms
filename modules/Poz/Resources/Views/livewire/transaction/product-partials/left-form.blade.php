<div class="col-md-8">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Code</label>
            <input disabled wire:model="form.code" type="text" class="form-control bg-light">
        </div>
        <div class="col-md-8">
            <label class="form-label small fw-bold">Nama Produk</label>
            <input type="text" class="form-control @error('form.name') is-invalid @enderror" wire:model="form.name">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">Tipe Produk</label>
            <select class="form-select @error('form.type') is-invalid @enderror" wire:model="form.type">
                <option value="">Pilih Tipe</option>
                <option value="1">Standard</option>
                <option value="2">Service</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">Brand</label>
            <select class="form-select" wire:model="form.brand_id">
                <option value="">Pilih Brand</option>
                @foreach ($brand as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">Kategori</label>
            <select class="form-select" wire:model="form.category_id" wire:change="sub_category_changed($event.target.value)">
                <option value="">Pilih Kategori</option>
                @foreach ($category as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">Sub Kategori</label>
            <select class="form-select" wire:model="form.sub_category_id" {{ $categoryHasSub != 1 ? 'disabled' : '' }}>
                <option value="">Pilih Sub</option>
                @foreach ($subCategory as $value) <option value="{{ $value->id }}">{{ $value->name }}</option> @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Unit</label>
            <select class="form-select @error('form.unit_id') is-invalid @enderror" wire:model="form.unit_id">
                <option value="">Pilih Unit</option>
                <option value="1">PCS</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-primary">Harga Modal</label>
            <input type="number" wire:model="form.wholesale" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Harga Jual</label>
            <input type="number" wire:model="form.price" class="form-control">
        </div>

        {{-- TinyMCE Biasa --}}
        <div class="col-12" wire:ignore>
            <label class="form-label small fw-bold">Deskripsi</label>
            <textarea id="description" class="form-control" wire:model="form.description"></textarea>
        </div>
    </div>
</div>
