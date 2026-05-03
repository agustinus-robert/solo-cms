@extends('tour::layouts.default')

@section('title', 'Ketersediaan Tour | ')

@section('navtitle', 'Manajemen Stok & Tanggal')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center" onsubmit="return false;">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted">Cari Paket</label>
                        <select name="package_id" class="form-select" onchange="fetchAvailabilities()">
                            <option value="">Semua Paket</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->tour->title }} - {{ $package->package_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted">Filter Tanggal</label>
                        <input type="date" name="date" class="form-control" onchange="fetchAvailabilities()">
                    </div>
                    <div class="col-md-5 text-md-end mt-md-4">
                        <button type="button" onclick="fetchAvailabilities()" class="btn btn-light border"><i class="mdi mdi-refresh"></i></button>
                        <a href="{{ route('tour::availability.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-calendar-plus me-1"></i> Atur Stok Tanggal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div id="table-container">
                @include('tour::availability._table')
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
    async function fetchAvailabilities(url = "{{ route('tour::availability.index') }}") {
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

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchAvailabilities(e.target.closest('.pagination a').href);
        }
    });

    function deleteAction(url) {
        if (confirm('Hapus pengaturan tanggal ini?')) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
