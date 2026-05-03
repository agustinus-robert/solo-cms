<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3">Nama Paket</th>
                <th>Master Tour</th>
                <th>Harga / Orang</th>
                <th>Fasilitas (Labels)</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages ?? [] as $package)
            <tr>
                <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $package->package_name }}</div>
                    <small class="text-muted text-uppercase" style="font-size: 10px;">ID: #PKG-{{ $package->id }}</small>
                </td>
                <td>
                    {{-- Tambahkan null check pada relasi tour --}}
                    <span class="text-muted">
                        <i class="mdi mdi-map-marker-path me-1"></i>
                        {{ $package->tour->title ?? 'Tour Tidak Ditemukan' }}
                    </span>
                </td>
                <td>
                    <span class="fw-bold text-primary">Rp {{ number_format($package->price_per_person ?? 0, 0, ',', '.') }}</span>
                </td>
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        {{-- Tambahkan ?? [] pada relasi labels --}}
                        @forelse($package->labels ?? [] as $label)
                            <span class="rounded-pill border px-2 py-1"
                                  style="color: {{ $label->color }}; border-color: {{ $label->color }} !important; background-color: {{ $label->color }}10;">
                                <i class="mdi {{ $label->icon ?? 'mdi-tag' }} me-1"></i>{{ $label->name }}
                            </span>
                        @empty
                            <span class="text-muted small italic">N/A</span>
                        @endforelse
                    </div>
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group shadow-sm">
                        <a href="{{ route('tour::package.edit', $package->id) }}" class="btn btn-sm btn-white border" title="Edit Paket">
                            <i class="mdi mdi-pencil text-warning"></i>
                        </a>
                        <button type="button" onclick="deleteAction('{{ route('tour::package.destroy', $package->id) }}')" class="btn btn-sm btn-white border text-danger" title="Hapus Paket">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="mdi mdi-package-variant-closed d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    <span class="text-muted">Belum ada paket tour yang dibuat.</span>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($packages) && $packages instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $packages->count() }} paket dari total {{ $packages->total() }}</small>
        <div>
            {{ $packages->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif
