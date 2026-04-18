@extends('hotel::layouts.default')

@section('title', 'Master Fasilitas | ')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Master Fasilitas (Amenities)</h4>
            <p class="text-muted small mb-0">Kelola daftar fasilitas yang tersedia untuk setiap tipe kamar.</p>
        </div>
        <a href="{{ route('hotel::amenity.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Tambah Fasilitas
        </a>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0" id="amenity-table-container">
                @include('hotel::amenity._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Refresh Tabel menggunakan Vanilla AJAX
     */
    function refreshTable() {
        const container = document.getElementById('amenity-table-container');

        fetch("{{ route('hotel::amenity.index') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => console.error('Error refreshing table:', error));
    }

    /**
     * Hapus Data via AJAX
     */
    function deleteAmenity(id) {
        if (confirm('Yakin ingin menghapus fasilitas ini?')) {
            fetch(`/hotel/amenity/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => {
                if (response.ok) {
                    refreshTable();
                } else {
                    alert('Gagal menghapus data. Pastikan fasilitas tidak sedang digunakan.');
                }
            })
            .catch(error => console.error('Error deleting:', error));
        }
    }
</script>
@endpush
