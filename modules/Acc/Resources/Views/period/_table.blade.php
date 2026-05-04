<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Nama Periode</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($periods as $item)
        <tr>
            <td class="fw-bold">{{ $item->name }}</td>
            <td>{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</td>
            <td class="text-center">
                @if($item->is_closed)
                    <span class="badge bg-danger">Closed</span>
                @else
                    <span class="badge bg-success">Open</span>
                @endif
            </td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="{{ route('acc::period.edit', $item->id) }}" class="btn btn-sm btn-light border">
                        <i class="mdi mdi-pencil text-primary"></i>
                    </a>
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-light border"
                       onclick="if(confirm('Hapus periode ini?')) {
                           fetch('{{ route('acc::period.destroy', $item->id) }}', {
                               method: 'DELETE',
                               headers: {
                                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                   'X-Requested-With': 'XMLHttpRequest'
                               }
                           }).then(res => window.location.reload())
                       }">
                        <i class="mdi mdi-trash-can text-danger"></i>
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">Belum ada data periode.</td>
        </tr>
        @endforelse
    </tbody>
</table>
