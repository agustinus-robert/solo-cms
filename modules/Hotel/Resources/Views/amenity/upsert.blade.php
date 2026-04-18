@extends('hotel::layouts.default')

@section('title', ($amenity ? 'Edit' : 'Tambah') . ' Fasilitas | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('hotel::amenity.index') }}" class="btn btn-light border me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">{{ $amenity ? 'Edit' : 'Tambah' }} Fasilitas</h4>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $amenity ? route('hotel::amenity.update', $amenity->id) : route('hotel::amenity.store') }}" method="POST">
                    @csrf
                    @if($amenity) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Fasilitas</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $amenity->name ?? '') }}"
                            placeholder="Contoh: WiFi Gratis, AC, Bathtub" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Icon (MDI Class)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="{{ old('icon', $amenity->icon ?? 'mdi mdi-help-circle') }}" id="icon-preview"></i>
                            </span>
                            <input type="text" name="icon" class="form-control" id="icon-input"
                                value="{{ old('icon', $amenity->icon ?? '') }}"
                                placeholder="mdi mdi-wifi"
                                onkeyup="document.getElementById('icon-preview').className = this.value || 'mdi mdi-help-circle'">
                        </div>
                        <div class="form-text small">
                            Cari icon di <a href="https://pictogrammers.com/library/mdi/" target="_blank" class="text-decoration-none">Material Design Icons</a>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hotel::amenity.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
