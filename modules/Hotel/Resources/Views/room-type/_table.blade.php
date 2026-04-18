<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Nama Tipe</th>
                    <th>Harga Dasar</th>
                    <th>Kapasitas</th>
                    <th class="text-center">Total Unit</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roomTypes as $type)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $type->name }}</div>
                            <small class="text-muted">{{ Str::limit($type->description, 50) }}</small>
                        </td>
                        <td>
                            <span class="fw-bold text-success">
                                Rp {{ number_format($type->base_price, 0, ',', '.') }}
                            </span>
                            <small class="text-muted">/malam</small>
                        </td>
                        <td>
                            <i class="mdi mdi-account-group me-1"></i>
                            {{ $type->capacity }} Orang
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border px-3">
                                {{ $type->rooms_count ?? 0 }} Kamar
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('hotel::room-types.edit', $type->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                    data-url="{{ route('hotel::room-types.destroy', $type->id) }}">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Belum ada tipe kamar yang dikonfigurasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($roomTypes instanceof \Illuminate\Pagination\LengthAwarePaginator && $roomTypes->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $roomTypes->links() }}
    </div>
@endif
