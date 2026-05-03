@extends('tour::layouts.default')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ $availability ? route('tour::availability.update', $availability->id) : route('tour::availability.store') }}" method="POST">
            @csrf
            @if($availability) @method('PUT') @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">{{ $availability ? 'Edit Stok Tanggal' : 'Tambah Stok Baru' }}</h5>
                </div>
                <div class="card-body">
                    {{-- Pilih Paket --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Paket Tour</label>
                        <select name="tour_package_id" class="form-select @error('tour_package_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ (old('tour_package_id', $availability->tour_package_id ?? '') == $package->id) ? 'selected' : '' }}>
                                    {{ $package->tour->title }} - {{ $package->package_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        {{-- Tanggal --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Tersedia</label>
                            <input type="date" name="available_date" class="form-control"
                                   value="{{ old('available_date', $availability ? $availability->available_date->format('Y-m-d') : '') }}" required>
                        </div>
                        {{-- Stok --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Stok (Pax)</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $availability->stock ?? 0) }}" min="0">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-check form-switch mt-2">
                        <input type="hidden" name="is_available" value="0">
                        <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvailable"
                               {{ old('is_available', $availability->is_available ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="isAvailable">Aktifkan Tanggal Ini</label>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <a href="{{ route('tour::availability.index') }}" class="btn btn-light border me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Stok</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
