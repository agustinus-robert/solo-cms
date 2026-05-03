@extends('tour::layouts.default')

@section('title', 'Master Lokasi | ')

@section('navtitle', 'Master Lokasi (Locations)')

@section('content')
<div class="row">
    {{-- Filter & Action --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center" onsubmit="return false;">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" id="search-input" class="form-control border-start-0" placeholder="Cari nama lokasi..." onkeyup="debounceFetch()">
                        </div>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <button type="button" onclick="fetchLocations()" class="btn btn-light border"><i class="mdi mdi-refresh"></i></button>
                        <a href="{{ route('tour::location.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-thick me-1"></i> Tambah Lokasi
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
                @include('tour::location._table')
            </div>
        </div>
    </div>
</div>

<form id="action-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    async function fetchLocations(url = "{{ route('tour::location.index') }}") {
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

    let timeout = null;
    function debounceFetch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchLocations(), 500);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchLocations(e.target.closest('.pagination a').href);
        }
    });

    function deleteAction(url) {
        if (confirm('Hapus lokasi ini?')) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
