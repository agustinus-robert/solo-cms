<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3">Nama Paket / Tour</th>
                <th>Lokasi Penjemputan</th>
                <th>Jam</th>
                <th>Meeting Point</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($times as $time)
            <tr>
                <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $time->package->package_name }}</div>
                    <small class="text-muted">{{ $time->package->tour->title ?? '-' }}</small>
                </td>
                <td>
                    <span class="badge bg-soft-info text-info">{{ $time->location->name }}</span>
                </td>
                <td>
                    <span class="fw-bold text-primary">{{ $time->formatted_time }}</span>
                </td>
                <td>
                    <small class="text-muted">{{ $time->meeting_point ?? '-' }}</small>
                </td>
                <td class="text-end pe-3">
                    <a href="{{ route('tour::package.times', $time->tour_package_id) }}" class="btn btn-sm btn-white border">
                        <i class="mdi mdi-eye-outline me-1"></i> Kelola
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    <i class="mdi mdi-calendar-blank-outline d-block mb-2 opacity-25" style="font-size: 3rem;"></i>
                    Tidak ada jadwal yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: {{ $times->total() }} Data Jadwal</small>
        <div>
            {{ $times->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
