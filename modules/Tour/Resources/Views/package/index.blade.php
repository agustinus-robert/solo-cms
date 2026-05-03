@extends('tour::layouts.default')

@section('title', 'Manajemen Paket Tour | ')

@section('navtitle', 'Daftar Paket Tour')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center" onsubmit="return false;">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase">Filter Master Tour</label>
                        <select name="tour_id" class="form-select border-0 bg-light" onchange="fetchPackages()">
                            <option value="">Semua Tour</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase">Cari Nama Paket</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search-input" class="form-control border-0 bg-light" placeholder="Misal: Paket VIP..." onkeyup="debounceFetch()">
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-md-4">
                        <button type="button" onclick="fetchPackages()" class="btn btn-light border"><i class="mdi mdi-refresh"></i></button>
                        <a href="{{ route('tour::package.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-box me-1"></i> Buat Paket Baru
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div id="table-container">
                @include('tour::package._table')
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
    async function fetchPackages(url = "{{ route('tour::package.index') }}") {
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
        timeout = setTimeout(() => fetchPackages(), 500);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchPackages(e.target.closest('.pagination a').href);
        }
    });

    function deleteAction(url) {
        if (confirm('Hapus paket ini? Data detail terkait juga akan terdampak.')) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
