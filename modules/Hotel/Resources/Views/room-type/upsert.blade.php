@extends('hotel::layouts.default')

@section('title', ($type ? 'Edit' : 'Tambah') . ' Tipe Kamar | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="mdi {{ $type ? 'mdi-layers-edit' : 'mdi-layers-plus' }} me-2 text-primary"></i>
                    {{ $type ? 'Edit Tipe Kamar: ' . $type->name : 'Tambah Tipe Kamar Baru' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $type ? route('hotel::room-types.update', $type->id) : route('hotel::room-types.store') }}" method="POST">
                    @csrf
                    @if($type)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nama Tipe Kamar</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $type->name ?? '') }}"
                                placeholder="Misal: Deluxe King, Suite, Standard Twin" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Harga Dasar --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Harga per Malam (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="base_price"
                                    class="form-control @error('base_price') is-invalid @enderror"
                                    value="{{ old('base_price', $type->base_price ?? '') }}"
                                    placeholder="0" required>
                            </div>
                            @error('base_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kapasitas Tamu</label>
                            <div class="input-group">
                                <input type="number" name="capacity"
                                    class="form-control @error('capacity') is-invalid @enderror"
                                    value="{{ old('capacity', $type->capacity ?? '2') }}"
                                    min="1" required>
                                <span class="input-group-text bg-light">Orang</span>
                            </div>
                            @error('capacity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Deskripsi Fasilitas</label>
                            <textarea name="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Ceritakan keunggulan tipe kamar ini (misal: AC, WiFi, Breakfast, City View)...">{{ old('description', $type->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="{{ route('hotel::room-types.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="mdi mdi-content-save me-1"></i> Simpan Tipe Kamar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .input-group-text { font-weight: 600; border-color: #dee2e6; }
    .card { border-radius: 12px; }
    textarea.form-control { resize: none; }
</style>
@endpush
