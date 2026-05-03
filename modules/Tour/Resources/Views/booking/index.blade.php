@extends('tour::layouts.default')

@section('title', 'Daftar Tour | ')

@section('navtitle', 'Manajemen Tour & Paket')

@section('content')
<div class="row">
    {{-- Ringkasan Status --}}
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <small class="opacity-75">Total Tour Terdaftar</small>
                        <h3 class="mb-0 fw-bold">{{ $tours->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <small class="opacity-75">Lokasi Aktif</small>
                        <h3 class="mb-0 fw-bold">{{ \Modules\Tour\Models\Tour::distinct('location')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Action --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center" onsubmit="return false;">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Cari Nama Tour atau Lokasi..." onkeyup="debounceFetch()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="label" class="form-select" onchange="fetchBookings()">
                            <option value="">Semua Fasilitas</option>
                            @foreach(\Modules\Tour\Models\TourLabel::all() as $label)
                                <option value="{{ $label->id }}">{{ $label->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <button type="button" onclick="fetchBookings()" class="btn btn-light border"><i class="mdi mdi-refresh"></i></button>
                        <a href="{{ route('tour::booking.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-thick me-1"></i> Buat Tour Baru
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div id="table-container">
                @include('tour::booking._table')
            </div>
        </div>
    </div>
</div>

{{-- Hidden Form untuk Action Delete --}}
<form id="action-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    // Fungsi Fetch Utama (Vanilla JS)
    async function fetchBookings(url = "{{ route('tour::booking.index') }}") {
        const container = document.getElementById('table-container');
        const filterForm = document.getElementById('filter-form');

        // Ambil data dari form filter
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();

        try {
            container.style.opacity = '0.5';
            const separator = url.includes('?') ? '&' : '?';
            const response = await fetch(`${url}${separator}${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            container.innerHTML = html;
        } catch (e) {
            console.error("Fetch Error:", e);
        } finally {
            container.style.opacity = '1';
        }
    }

    // Debounce untuk Search agar tidak berat
    let timeout = null;
    function debounceFetch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            fetchBookings();
        }, 500);
    }

    // Handle Pagination Links
    document.addEventListener('click', function(e) {
        const anchor = e.target.closest('.pagination a');
        if (anchor) {
            e.preventDefault();
            fetchBookings(anchor.href);
        }
    });

    // Handle Delete Action
    function deleteAction(url) {
        if (confirm('Apakah Anda yakin ingin menghapus tour ini? Semua paket di dalamnya akan ikut terhapus.')) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
