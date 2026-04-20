@extends('hotel::layouts.default')

@section('title', ($inventory ? 'Edit' : 'Tambah') . ' Barang Inventaris | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('hotel::inventory.index') }}" class="btn btn-light border me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">{{ $inventory ? 'Edit' : 'Tambah' }} Barang Inventaris</h4>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $inventory ? route('hotel::inventory.update', $inventory->id) : route('hotel::inventory.store') }}" method="POST">
                    @csrf
                    @if($inventory) @method('PUT') @endif

                    <div class="row">
                        {{-- Nama Barang --}}
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Barang</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $inventory->name ?? '') }}"
                                placeholder="Contoh: Bantal King Coil, Sabun Cair 100ml" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tipe Barang --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tipe</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach(\Modules\Hotel\Enums\InventoryTypeEnum::cases() as $type)
                                    <option value="{{ $type->value }}"
                                        {{ old('type', $inventory?->type?->value) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- Stok Saat Ini --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Stok Saat Ini</label>
                            <div class="input-group">
                                <input type="number" name="total_stock" class="form-control @error('total_stock') is-invalid @enderror"
                                    value="{{ old('total_stock', $inventory->total_stock ?? 0) }}" min="0" required>
                            </div>
                            @error('total_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Satuan --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror"
                                value="{{ old('unit', $inventory->unit ?? 'pcs') }}" placeholder="pcs, botol, pack" required>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Minimal Stok Alert --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Minimal Stok (Alert)</label>
                            <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror"
                                value="{{ old('min_stock', $inventory->min_stock ?? 5) }}" min="0" required>
                            @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Deskripsi / Lokasi Gudang</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Contoh: Rak A-1, Merk King Coil, warna putih...">{{ old('description', $inventory->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hotel::inventory.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Inventaris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
