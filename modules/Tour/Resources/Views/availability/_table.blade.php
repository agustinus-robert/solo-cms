<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3">Tanggal</th>
                <th>Paket Tour</th>
                <th class="text-center">Stok Sisa</th>
                <th class="text-center">Status</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($availabilities as $item)
            <tr>
                <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $item->available_date->format('d M Y') }}</div>
                    <small class="text-muted">{{ $item->available_date->format('l') }}</small>
                </td>
                <td>
                    <div class="text-dark fw-semibold">{{ $item->package->tour->title }}</div>
                    <div class="badge bg-light text-primary border-0">{{ $item->package->package_name }}</div>
                </td>
                <td class="text-center">
                    @if($item->stock <= 5 && $item->stock > 0)
                        <span class="badge bg-warning-subtle text-warning border border-warning">Hampir Habis: {{ $item->stock }}</span>
                    @elseif($item->stock == 0)
                        <span class="badge bg-danger-subtle text-danger border border-danger">Sold Out</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success">{{ $item->stock }} Tersedia</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($item->is_available)
                        <span class="text-success"><i class="mdi mdi-check-circle me-1"></i>Open</span>
                    @else
                        <span class="text-danger"><i class="mdi mdi-close-circle me-1"></i>Closed</span>
                    @endif
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group shadow-sm">
                        <a href="{{ route('tour::availability.edit', $item->id) }}" class="btn btn-sm btn-white border">
                            <i class="mdi mdi-pencil text-warning"></i>
                        </a>
                        <button type="button" onclick="deleteAction('{{ route('tour::availability.destroy', $item->id) }}')" class="btn btn-sm btn-white border text-danger">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="mdi mdi-calendar-remove-outline d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    <span class="text-muted">Tidak ada jadwal ketersediaan untuk kriteria ini.</span>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: {{ $availabilities->total() }} Data</small>
        <div>
            {{ $availabilities->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
