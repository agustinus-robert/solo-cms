<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4" width="150">Kode Akun</th>
                <th>Nama Akun</th>
                <th>Kategori</th>
                <th class="text-end pe-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coas as $coa)
                <tr>
                    <td class="ps-4 fw-bold text-primary">{{ $coa->code }}</td>
                    <td>{{ $coa->name }}</td>
                    <td>
                        <span class="badge bg-soft-info text-info border border-info px-2">
                            {{ strtoupper($coa->category->value) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="{{ route('acc::coa.edit', $coa->id) }}" class="btn btn-sm btn-light border">
                                <i class="mdi mdi-pencil text-warning"></i>
                            </a>
                            <a href="javascript:void(0)"
                            class="btn btn-sm btn-light border"
                            onclick="if(confirm('Hapus akun ini?')) {
                                fetch('{{ route('acc::coa.destroy', $coa->id) }}', {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }).then(res => res.ok ? reloadTable() : alert('Gagal hapus'))
                            }">
                                <i class="mdi mdi-trash-can text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Data akun tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-4 py-3 border-top">
        <div class="row align-items-center">
            <div class="col-sm-6 text-muted">
                Menampilkan {{ $coas->firstItem() }} sampai {{ $coas->lastItem() }} dari {{ $coas->total() }} data
            </div>
            <div class="col-sm-6">
                <div class="float-end pagination-laravel">
                    {{ $coas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
