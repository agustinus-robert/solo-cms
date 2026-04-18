@extends('hotel::layouts.default')

@section('title', 'Daftar Reservasi | ')

@section('navtitle', 'Booking & Reservasi')

@section('content')
<div class="row">
    {{-- Ringkasan Status --}}
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <small class="opacity-75">Total Booking</small>
                        <h3 class="mb-0 fw-bold">{{ $bookings->total() }}</h3>
                    </div>
                </div>
            </div>
            {{-- Tambahkan widget status lainnya di sini --}}
        </div>
    </div>

    {{-- Filter & Action --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Cari Kode, Nama Tamu, atau Kamar...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="fetchBookings()">
                            <option value="">Semua Status</option>
                            @foreach(\Modules\Hotel\Enums\BookingStatusEnum::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <button type="button" onclick="fetchBookings()" class="btn btn-light border"><i class="mdi mdi-refresh"></i></button>
                        <a href="{{ route('hotel::booking.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-thick me-1"></i> Reservasi Baru
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
                @include('hotel::booking._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function fetchBookings(url = "{{ route('hotel::booking.index') }}") {
        const container = document.getElementById('table-container');
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData).toString();

        try {
            container.style.opacity = '0.5';
            const response = await fetch(`${url}${url.includes('?') ? '&' : '?'}${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            container.innerHTML = html;
        } catch (e) {
            console.error(e);
        } finally {
            container.style.opacity = '1';
        }
    }

    // Pagination handle
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchBookings(e.target.closest('.pagination a').href);
        }
    });

    function confirmAction(url, message) {
        if (confirm(message)) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
