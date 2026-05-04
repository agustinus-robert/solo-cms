@extends('tour::layouts.default')

@section('title', 'Manajemen Review | ')

@section('navtitle', 'Ulasan Pengunjung')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filter-form" class="row g-3" onsubmit="return false;">
                    <div class="col-md-4">
                        <select name="tour_id" class="form-select" onchange="fetchData()">
                            <option value="">-- Semua Tour --</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="rating" class="form-select" onchange="fetchData()">
                            <option value="">-- Semua Rating --</option>
                            @for($i=5; $i>=1; $i--)
                                <option value="{{ $i }}">{{ $i }} Bintang</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari isi ulasan..." onkeyup="debounceFetch()">
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" onclick="fetchData()" class="btn btn-light border w-100">
                            <i class="mdi mdi-refresh"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div id="table-container">
                @include('tour::review._table')
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    async function fetchData(url = "{{ route('tour::tour-review.index') }}") {
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
        timeout = setTimeout(() => fetchData(), 500);
    }
</script>
@endpush
