@extends('hotel::layouts.default')

@section('title', 'Tipe Kamar | ')

@section('navtitle', 'Manajemen Tipe Kamar')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0 fw-bold">Daftar Kategori & Harga Kamar</h6>
                            <small class="text-muted">Kelola harga dasar dan kapasitas tamu.</small>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="btn-refresh" class="btn btn-light border me-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                            <a href="{{ route('hotel::room-types.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah Tipe
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div id="table-container">
                    @include('hotel::room-type._table')
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
        .card { border-radius: 12px; }
        #table-container { transition: opacity 0.2s ease-in-out; }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('table-container');
        const btnRefresh = document.getElementById('btn-refresh');

        async function fetchRoomTypes(url = "{{ route('hotel::room-types.index') }}") {
            try {
                tableContainer.style.opacity = '0.5';
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Network error');

                const html = await response.text();
                tableContainer.innerHTML = html;
            } catch (error) {
                alert('Gagal mengambil data tipe kamar.');
            } finally {
                tableContainer.style.opacity = '1';
            }
        }

        btnRefresh.addEventListener('click', () => fetchRoomTypes());

        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchRoomTypes(link.getAttribute('href'));
            }
        });

        tableContainer.addEventListener('click', async function(e) {
            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                if (confirm('Hapus tipe kamar ini? Seluruh unit kamar dengan tipe ini mungkin akan terdampak.')) {
                    const url = btnDelete.dataset.url;
                    try {
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const res = await response.json();
                        if (response.ok) {
                            fetchRoomTypes();
                        } else {
                            alert(res.error || 'Gagal menghapus.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan sistem.');
                    }
                }
            }
        });
    });
</script>
@endpush
