@extends('acc::layouts.default')

@section('title', 'Daftar Akun (COA) | ')

@section('content')
<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-format-list-bulleted me-2"></i>Chart of Accounts</h5>
                <a href="{{ route('acc::coa.create') }}" class="btn btn-primary shadow-sm">
                    <i class="mdi mdi-plus me-1"></i> Tambah Akun
                </a>
            </div>
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="search-input" class="form-control" placeholder="Cari kode atau nama akun...">
                    </div>
                </div>
            </div>
            <div id="table-container">
                @include('acc::coa._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Fungsi utama untuk reload tabel via AJAX Fetch
     */
    async function reloadTable(url = "{{ route('acc::coa.index') }}") {
        const search = document.getElementById('search-input').value;
        const container = document.getElementById('table-container');

        const fetchUrl = new URL(url);
        fetchUrl.searchParams.set('search', search);

        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            container.innerHTML = html;
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    /**
     * Event Listener untuk Search (Debounce sederhana)
     */
    let searchTimer;
    document.getElementById('search-input').addEventListener('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            reloadTable();
        }, 300); // Tunggu 300ms setelah mengetik
    });

    /**
     * Event Listener untuk Paginasi (Delegation)
     */
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            reloadTable(paginationLink.href);
        }
    });

    /**
     * Fungsi Delete Data
     */
    async function deleteData(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus akun ini?')) return;

        try {
            const response = await fetch(`{{ url('acc/master/coa') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                reloadTable();
            } else {
                alert(result.message || 'Gagal menghapus data');
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Terjadi kesalahan pada server');
        }
    }
</script>
@endpush
