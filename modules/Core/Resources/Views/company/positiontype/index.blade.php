@extends('core::layouts.default')

@section('title', 'Tipe Posisi | ')
@section('navtitle', 'tipe posisi')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-primary text-uppercase tracking-wide">Position Types</h4>
                    <p class="text-muted small mb-0">Total data ditemukan: {{ $items->total() }}</p>
                </div>
                <a href="{{ route('core::company.position-type.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Baru
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4 rounded-4">
                <div class="card-body p-3">
                    <form action="{{ route('core::company.position-type.index') }}" method="GET" class="row g-2">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                       placeholder="Cari berdasarkan Kode atau Nama..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold" style="width: 15%">Kode</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold">Nama Tipe</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold">Kategori</th>
                                <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Status</th>
                                <th class="pe-4 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border fw-medium px-3 py-2">{{ $item->kd }}</span>
                                </td>
                                <td class="fw-semibold text-dark">{{ $item->name }}</td>
                                <td>
                                    <span class="text-muted small">{{ $item->category ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('core::company.position-type.edit', $item->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-circle p-2"
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="mdi mdi-pencil-outline px-1"></i>
                                        </a>

                                        <form action="{{ route('core::company.position-type.destroy', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin mau hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                <i class="mdi mdi-trash-can-outline px-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bi bi-folder-x display-4"></i></div>
                                    Belum ada data tipe posisi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-subtle { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; color: #842029 !important; }
</style>
@endsection
