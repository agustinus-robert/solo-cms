@extends('tour::layouts.default')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ $package ? route('tour::package.update', $package->id) : route('tour::package.store') }}" method="POST">
            @csrf
            @if($package) @method('PUT') @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">{{ $package ? 'Edit Paket Tour' : 'Tambah Paket Baru' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Pilih Master Tour --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Master Tour</label>
                            <select name="tour_id" class="form-select border-0 bg-light shadow-none" required>
                                <option value="">-- Pilih Induk Tour --</option>
                                @foreach($tours ?? [] as $tour)
                                    <option value="{{ $tour->id }}" {{ (old('tour_id', $package->tour_id ?? '') == $tour->id) ? 'selected' : '' }}>
                                        {{ $tour->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nama Paket --}}
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Paket</label>
                            <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $package->package_name ?? '') }}" placeholder="Misal: Paket Full Service" required>
                        </div>

                        {{-- Harga --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Harga / Orang</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light text-muted">Rp</span>
                                <input type="number" name="price_per_person" class="form-control" value="{{ old('price_per_person', $package ? number_format($package->price_per_person, 0, '', '') : '') }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section Label/Fasilitas --}}
                    <div class="mt-4">
                        <label class="form-label fw-bold mb-2 d-block">Pilih Fasilitas & Label</label>
                        <div class="row g-2">
                            @php
                                $dbLabelIds = ($package && $package->labels) ? $package->labels->pluck('id')->toArray() : [];
                                $selectedLabelIds = old('label_ids', $dbLabelIds);
                            @endphp

                            @forelse($labels ?? [] as $label)
                                <div class="col-md-4">
                                    <div class="border rounded p-2 d-flex align-items-center h-100 transition-hover">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                name="label_ids[]"
                                                value="{{ $label->id }}"
                                                id="label-{{ $label->id }}"
                                                {{ in_array($label->id, $selectedLabelIds) ? 'checked' : '' }}>

                                            <label class="form-check-label ms-2 d-flex align-items-center" for="label-{{ $label->id }}" style="cursor: pointer;">
                                                <i class="mdi {{ $label->icon ?? 'mdi-tag-outline' }} fs-5 me-2"
                                                   style="color: {{ $label->color ?? '#6c757d' }}"></i>
                                                <span class="small fw-semibold text-dark">{{ $label->name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning py-2 small">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                                        Master Label belum ada. <a href="{{ route('tour::label.index') }}" class="fw-bold">Buat dulu di sini.</a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white py-3 text-end">
                    <a href="{{ route('tour::package.index') }}" class="btn btn-light border me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Paket
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .transition-hover:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6 !important;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush
