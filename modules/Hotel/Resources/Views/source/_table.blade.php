<table class="table align-middle mb-0">
    <thead class="bg-light">
        <tr>
            <th class="ps-4">Nama Sumber</th>
            <th class="text-center">Rate Komisi</th>
            <th>Terakhir Diupdate</th>
            <th class="text-end pe-4">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sources as $source)
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs bg-soft-info rounded p-2 me-2 text-center">
                            <i class="mdi mdi-earth text-info"></i>
                        </div>
                        <span class="fw-bold text-dark">{{ $source->name }}</span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-soft-warning text-warning px-3">
                        {{ number_format($source->commission_rate, 0) }}%
                    </span>
                </td>
                <td>{{ $source->updated_at->diffForHumans() }}</td>
                <td class="text-end pe-4">
                    <a href="{{ route('hotel::source.edit', $source->id) }}" class="btn btn-sm btn-light border me-1">
                        <i class="mdi mdi-pencil-outline"></i>
                    </a>
                    <button class="btn btn-sm btn-light border text-danger" onclick="deleteSource({{ $source->id }})">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="mdi mdi-database-off fs-1 d-block mb-2"></i>
                    Belum ada data sumber reservasi.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
