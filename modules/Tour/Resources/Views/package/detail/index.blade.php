@extends('tour::layouts.default')

@section('title', 'Detail Paket | ')

@section('navtitle')
    Detail: {{ $package->package_name }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Form Tambah Detail -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Tambah Itinerary / Info</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('tour::package.detail.store', ["package" => $package->id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Label / Judul</label>
                        <input type="text" name="label" class="form-control" placeholder="Contoh: Hari 01, Inklusi, atau Catatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konten / Penjelasan</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Tulis rincian di sini..." required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Tambah Detail
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="mdi mdi-information-outline fs-2 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 fw-bold">Tips</h6>
                        <small class="text-muted">Gunakan label "Hari 01" untuk Itinerary, atau "Syarat" untuk info tambahan agar rapi di tampilan user.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- List Detail yang Sudah Ada -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Daftar Rincian Paket</h6>
                <span class="badge bg-light text-primary">{{ $details->count() }} Item</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">Urutan</th>
                                <th width="150">Label</th>
                                <th>Konten</th>
                                <th width="100" class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-details">
                            @forelse($details as $detail)
                            <tr data-id="{{ $detail->id }}">
                                <td class="text-center text-muted cursor-move">
                                    <i class="mdi mdi-reorder-horizontal fs-5"></i>
                                </td>
                                <td><span class="fw-bold">{{ $detail->label }}</span></td>
                                <td>
                                    <div class="text-wrap small" style="max-width: 300px;">
                                        {!! nl2br(e($detail->content)) !!}
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('tour::package.detail.destroy', ["package" => $detail->id]) }}" method="POST" onsubmit="return confirm('Hapus detail ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Belum ada detail. Tambahkan itinerary atau informasi paket di sebelah kiri.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-2">
                <small class="text-muted"><i class="mdi mdi-lightbulb-on-outline me-1"></i> Detail ini akan tampil di tab informasi pada halaman frontend tour.</small>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('tour::package.index') }}" class="btn btn-link text-muted p-0 text-decoration-none">
                <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar Paket
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-move { cursor: move; }
    .table-hover tbody tr:hover { background-color: #fcfcfc; }
    .sortable-ghost { opacity: 0.5; background: #f0f0f0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('sortable-details');

        if (el) {
            Sortable.create(el, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'sortable-ghost',
                forceFallback: true, // Penting untuk drag & drop di elemen table
                onEnd: function () {
                    saveOrder();
                },
            });
        }
    });

    async function saveOrder() {
        const rows = document.querySelectorAll('#sortable-details tr');
        let orders = [];

        rows.forEach((row, index) => {
            const id = row.getAttribute('data-id');
            if (id) {
                orders.push({
                    id: id,
                    position: index + 1
                });
            }
        });

        if (orders.length === 0) return;

        try {
            const response = await fetch("{{ route('tour::package.detail.update-order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ orders: orders })
            });

            const result = await response.json();
            if(result.status === 'success') {
                console.log('Urutan berhasil disimpan');
            }
        } catch (error) {
            console.error('Gagal menyimpan urutan:', error);
        }
    }
</script>
@endpush
