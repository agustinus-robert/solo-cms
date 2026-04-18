<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small fw-bold">
                <tr>
                    <th class="ps-4" style="width: 120px;">No. Kamar</th>
                    <th>Tipe & Harga</th>
                    <th>Lantai</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $room->room_number }}</td>
                        <td>
                            <div class="fw-bold">{{ $room->type->name }}</div>
                            <small class="text-muted">Rp {{ number_format($room->type->base_price, 0, ',', '.') }}</small>
                        </td>
                        <td>Lantai {{ $room->floor }}</td>
                        <td>
                            @php
                                $badgeClass = match($room->status->value) {
                                    1 => 'bg-success',
                                    2 => 'bg-danger',
                                    3 => 'bg-warning text-dark',
                                    4 => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                {{ $room->status->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                {{-- Tombol Edit Tetap Pakai Link --}}
                                <a href="{{ route('hotel::room.edit', $room->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>

                                {{-- Tombol Hapus Menggunakan Form --}}
                                <form action="{{ route('hotel::room.destroy', $room->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus Kamar {{ $room->room_number }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="mdi mdi-door-closed mdi-48px opacity-25"></i>
                            <p class="mt-2 mb-0">Data kamar tidak ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($rooms->hasPages())
    <div class="card-footer bg-white border-top-0 py-3" id="pagination-links">
        {{ $rooms->appends(request()->all())->links() }}
    </div>
@endif
