@extends('hotel::layouts.default')

@section('title', 'Master Inventaris | ')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Master Inventaris & Stok</h4>
            <p class="text-muted small mb-0">Kelola aset kamar dan barang habis pakai (supplies) hotel.</p>
        </div>
        <a href="{{ route('hotel::inventory.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Tambah Barang
        </a>
    </div>

    {{-- Widget Ringkas --}}
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-soft-danger">
                    <div class="card-body">
                        <h6 class="text-danger mb-1">Stok Menipis</h6>
                        <h3 class="fw-bold mb-0">{{ $inventories->where('total_stock', '<=', 'min_stock')->count() }} <small class="fs-6">Item</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0" id="inventory-table-container">
                @include('hotel::inventory._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Refresh Tabel (Vanilla JS)
     */
    function refreshTable() {
        const container = document.getElementById('inventory-table-container');

        fetch("{{ route('hotel::inventory.index') }}", {
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
     * Hapus Data (Vanilla JS)
     */
    function deleteInventory(id) {
        if (confirm('Yakin ingin menghapus barang ini dari inventaris?')) {
            fetch(`/hotel/inventory/${id}`, {
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
                    alert('Gagal menghapus data.');
                }
            })
            .catch(error => console.error('Error deleting:', error));
        }
    }
</script>
@endpush
