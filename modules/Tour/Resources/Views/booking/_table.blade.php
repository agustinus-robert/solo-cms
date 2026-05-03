<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3" style="width: 40%">Informasi Tour</th>
                <th>Lokasi</th>
                <th>Harga Dasar</th>
                <th>Paket Tersedia</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tours as $tour)
            <tr>
                <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $tour->title }}</div>
                    <small class="text-muted">Slug: {{ $tour->slug }}</small>
                </td>
                <td>
                    <span class="badge bg-light text-dark border-0">
                        <i class="mdi mdi-map-marker-outline text-danger"></i> {{ $tour->location }}
                    </span>
                </td>
                <td>
                    <span class="fw-bold text-primary">Rp {{ number_format($tour->base_price, 0, ',', '.') }}</span>
                </td>
                <td>
                    {{-- Menggunakan withCount(['packages']) dari Repository --}}
                    <span class="badge rounded-pill {{ $tour->packages_count > 0 ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}">
                        {{ $tour->packages_count ?? 0 }} Paket
                    </span>
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group shadow-sm" role="group">
                        <a href="{{ route('tour::booking.edit', ["booking" => $tour->id]) }}"
                           class="btn btn-sm btn-white border"
                           title="Edit Tour">
                            <i class="mdi mdi-pencil text-warning"></i>
                        </a>
                        <button type="button"
                                onclick="deleteAction('{{ route('tour::booking.destroy', ['booking' => $tour->id]) }}')"
                                class="btn btn-sm btn-white border text-danger"
                                title="Hapus Tour">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="mdi mdi-database-off-outline d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    <span class="text-muted">Belum ada data tour yang terdaftar.</span>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $tours->firstItem() ?? 0 }} sampai {{ $tours->lastItem() ?? 0 }} dari {{ $tours->total() }} data
        </small>
        <div>
            {{ $tours->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
