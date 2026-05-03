<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3" width="50">#</th>
                <th>Nama Lokasi</th>
                <th>Slug</th>
                <th class="text-end pe-3" width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $index => $item)
            <tr>
                <td class="ps-3 text-muted">
                    {{ $locations->firstItem() + $index }}
                </td>
                <td>
                    <span class="fw-bold text-dark">{{ $item->name }}</span>
                </td>
                <td>
                    <code class="text-muted">{{ $item->slug }}</code>
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group shadow-sm">
                        <a href="{{ route('tour::location.edit', $item->id) }}" class="btn btn-sm btn-white border">
                            <i class="mdi mdi-pencil text-warning"></i>
                        </a>
                        <button type="button"
                                onclick="deleteAction('{{ route('tour::location.destroy', $item->id) }}')"
                                class="btn btn-sm btn-white border text-danger">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="mdi mdi-map-marker-off-outline d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    Belum ada data lokasi tour.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: {{ $locations->total() }} Lokasi</small>
        <div>
            {{ $locations->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
