@extends('hotel::layouts.default')

@section('title', 'Data Tamu | ')

@section('navtitle', 'Manajemen Tamu')

@section('content')
    <div class="row">
        {{-- Header & Search --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="filter-form" class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input type="text" name="search" id="search-input"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="Cari Nama, NIK, atau No. HP Tamu..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button type="button" id="btn-reset" class="btn btn-light border me-1">Reset</button>
                            <a href="{{ route('hotel::guest.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-account-plus me-1"></i> Registrasi Tamu
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
                    @include('hotel::guests._table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('table-container');
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const btnReset = document.getElementById('btn-reset');

        async function fetchGuests(url = "{{ route('hotel::guest.index') }}") {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            const finalUrl = url.includes('?') ? `${url}&${params}` : `${url}?${params}`;

            try {
                tableContainer.style.opacity = '0.5';
                const response = await fetch(finalUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Network Error');

                const html = await response.text();
                tableContainer.innerHTML = html;
            } catch (error) {
                console.error(error);
            } finally {
                tableContainer.style.opacity = '1';
            }
        }

        let timeout = null;
        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchGuests();
            }, 500);
        });

        btnReset.addEventListener('click', () => {
            filterForm.reset();
            fetchGuests();
        });

        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchGuests(link.getAttribute('href'));
            }
        });
    });
</script>
@endpush
