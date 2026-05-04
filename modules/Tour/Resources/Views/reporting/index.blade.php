@extends('tour::layouts.default')

@section('title', 'Laporan Jadwal Keberangkatan | ')

@section('navtitle', 'Laporan Jadwal Keberangkatan')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3 align-items-center" onsubmit="return false;">
                    <div class="col-md-3">
                        <select name="package_id" class="form-select" onchange="fetchReport()">
                            <option value="">-- Semua Paket --</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->package_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="location_id" class="form-select" onchange="fetchReport()">
                            <option value="">-- Semua Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari meeting point..." onkeyup="debounceFetch()">
                        </div>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <button type="button" onclick="fetchReport()" class="btn btn-light border w-100"><i class="mdi mdi-refresh"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div id="table-container">
                @include('tour::reporting._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function fetchReport(url = "{{ route('tour::package.report') }}") {
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
        } catch (e) { console.error(e); } finally { container.style.opacity = '1'; }
    }

    let timeout = null;
    function debounceFetch() {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchReport(), 500);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchReport(e.target.closest('.pagination a').href);
        }
    });
</script>
@endpush
