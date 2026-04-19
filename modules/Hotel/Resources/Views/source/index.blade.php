@extends('hotel::layouts.default')

@section('title', 'Sumber Reservasi | ')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Sumber Reservasi (Booking Sources)</h4>
            <p class="text-muted small mb-0">Kelola asal pemesanan tamu (OTA, Direct, dll) dan rate komisi.</p>
        </div>
        <a href="{{ route('hotel::source.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Tambah Sumber
        </a>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0" id="source-table-container">
                @include('hotel::source._table')
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
        const container = document.getElementById('source-table-container');

        fetch("{{ route('hotel::source.index') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => console.error('Error refreshing table:', error));
    }

    /**
     * Hapus Data via Fetch API
     */
    function deleteSource(id) {
        if (confirm('Yakin ingin menghapus sumber reservasi ini?')) {
            fetch(`/hotel/source/${id}`, {
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
                    alert('Gagal menghapus! Data mungkin masih digunakan dalam transaksi.');
                }
            })
            .catch(error => console.error('Error deleting:', error));
        }
    }
</script>
@endpush
