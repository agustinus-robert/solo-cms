@extends('hotel::layouts.default')

@section('title', 'Manajemen Kamar | ')

@section('navtitle', 'Daftar Kamar')

@section('content')
    <div class="row">
        {{-- Filter Section --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cari Berdasarkan Tipe</label>
                            <select name="type_id" class="form-select filter-input">
                                <option value="">Semua Tipe Kamar</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status Kamar</label>
                            <select name="status" class="form-select filter-input">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="btn-reset" class="btn btn-light border me-2">Reset</button>
                            <a href="{{ route('hotel::room.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah Kamar
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
                    @include('hotel::room._table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table thead th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            color: #6c757d;
        }
        .badge { font-weight: 600; border-radius: 6px; }
        .card { border-radius: 12px; }
        #table-container { transition: opacity 0.2s ease-in-out; }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('table-container');
        const filterForm = document.getElementById('filter-form');
        const filterInputs = document.querySelectorAll('.filter-input');
        const btnReset = document.getElementById('btn-reset');

        async function fetchRooms(url = "{{ route('hotel::room.index') }}") {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            const finalUrl = url.includes('?') ? `${url}&${params}` : `${url}?${params}`;

            try {
                tableContainer.style.opacity = '0.5';

                const response = await fetch(finalUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const html = await response.text();
                tableContainer.innerHTML = html;
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengambil data. Koplak!');
            } finally {
                tableContainer.style.opacity = '1';
            }
        }

        filterInputs.forEach(input => {
            input.addEventListener('change', () => fetchRooms());
        });

        btnReset.addEventListener('click', function() {
            filterForm.reset();
            fetchRooms();
        });

        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchRooms(link.getAttribute('href'));
            }
        });

        tableContainer.addEventListener('click', async function(e) {
            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                if (confirm('Yakin ingin menghapus kamar ini?')) {
                    const url = btnDelete.dataset.url;
                    try {
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) fetchRooms();
                    } catch (error) {
                        alert('Gagal menghapus data.');
                    }
                }
            }
        });
    });
</script>
@endpush
