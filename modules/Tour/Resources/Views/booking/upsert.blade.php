@extends('tour::layouts.default')

@section('title', ($tour ? 'Edit Tour' : 'Tambah Tour Baru') . ' | ')

@section('navtitle', $tour ? 'Edit Tour' : 'Tambah Tour Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ $tour ? route('tour::booking.update', $tour->id) : route('tour::booking.store') }}"
              method="POST"
              id="upsert-form"
              enctype="multipart/form-data">
            @csrf
            @if($tour)
                @method('PUT')
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary fw-bold">{{ $tour ? 'Informasi Detail Tour' : 'Buat Master Tour Baru' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Nama Tour --}}
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nama Tour <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $tour->title ?? '') }}" placeholder="Contoh: Paket Wisata Nusa Penida" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Lokasi / Daerah --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Lokasi / Daerah <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location', $tour->location ?? '') }}" placeholder="Contoh: Bali" required>
                        </div>

                        {{-- Harga Dasar --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Harga Dasar (Mulai Dari)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="base_price" class="form-control"
                                       value="{{ old('base_price', $tour->base_price ?? 0) }}">
                            </div>
                        </div>

                        {{-- Jam Buka --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jam Operasional</label>
                            <input type="text" name="opening_hours" class="form-control"
                                   value="{{ old('opening_hours', $tour->opening_hours ?? '') }}" placeholder="Contoh: 08:00 - 17:00">
                        </div>

                        {{-- Highlights --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                Activity Highlights
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addHighlight()">
                                    <i class="mdi mdi-plus"></i> Tambah Point
                                </button>
                            </label>
                            <div id="highlights-container">
                                @php
                                    $highlights = old('highlights', $tour->highlights ?? ['']);
                                @endphp
                                @foreach($highlights as $h)
                                    <div class="input-group mb-2 highlight-item">
                                        <input type="text" name="highlights[]" class="form-control" value="{{ $h }}" placeholder="Contoh: Golden Hour View">
                                        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Poin-poin menarik dari tour ini.</small>
                        </div>

                        {{-- Overview --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Gambaran Besar (Overview)</label>
                            <textarea name="overview" class="form-control @error('overview') is-invalid @enderror" rows="5" placeholder="Tuliskan deskripsi lengkap tour di sini...">{{ old('overview', $tour->overview ?? '') }}</textarea>
                            @error('overview') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <a href="{{ route('tour::booking.index') }}" class="btn btn-light border me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save me-1"></i> {{ $tour ? 'Simpan Perubahan' : 'Buat Master Tour' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Vanilla JS untuk menambah input highlight
     */
    function addHighlight() {
        const container = document.getElementById('highlights-container');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 highlight-item';
        div.innerHTML = `
            <input type="text" name="highlights[]" class="form-control" placeholder="Contoh: Aktivitas baru...">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                <i class="mdi mdi-close"></i>
            </button>
        `;
        container.appendChild(div);
    }

    /**
     * Validasi Form Sederhana
     */
    document.getElementById('upsert-form').addEventListener('submit', function(e) {
        const title = document.querySelector('input[name="title"]').value.trim();
        const location = document.querySelector('input[name="location"]').value.trim();

        if (!title || !location) {
            e.preventDefault();
            alert('Nama Tour dan Lokasi wajib diisi!');
        }
    });
</script>
@endpush
