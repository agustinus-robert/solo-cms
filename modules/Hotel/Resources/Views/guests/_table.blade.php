<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">No. Identitas</th>
                    <th>Nama Lengkap</th>
                    <th>Kontak</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-primary">{{ $guest->id_card_number }}</span>
                        </td>
                        <td>
                            {{-- Menggunakan Accessor getFullNameAttribute --}}
                            <div class="fw-bold text-dark">{{ $guest->full_name }}</div>
                        </td>
                        <td>
                            <div><i class="mdi mdi-phone me-1 text-success"></i>{{ $guest->phone_number }}</div>
                            <div class="small text-muted"><i class="mdi mdi-email me-1"></i>{{ $guest->email ?? '-' }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('hotel::guest.edit', $guest->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-account-edit"></i>
                                </a>
                                <form action="{{ route('hotel::guest.destroy', $guest->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data tamu ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Data tamu tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
