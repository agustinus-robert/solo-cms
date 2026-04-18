<table class="table align-middle mb-0">
    <thead class="bg-light">
        <tr>
            <th class="ps-4" style="width: 50px;">Icon</th>
            <th>Nama Fasilitas</th>
            <th>Dibuat Pada</th>
            <th class="text-end pe-4">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($amenities as $amenity)
            <tr>
                <td class="ps-4">
                    <div class="avatar-sm bg-soft-primary rounded p-2 text-center">
                        <i class="mdi {{ $amenity->icon ?: 'mdi-check-circle-outline' }} fs-4 text-primary"></i>
                    </div>
                </td>
                <td>
                    <span class="fw-bold text-dark">{{ $amenity->name }}</span>
                </td>
                <td>{{ $amenity->created_at->format('d M Y') }}</td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-light border text-danger" onclick="deleteAmenity({{ $amenity->id }})">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="mdi mdi-information-outline fs-1 d-block mb-2"></i>
                    Belum ada data fasilitas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
