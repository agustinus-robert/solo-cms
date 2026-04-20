<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">Riwayat Adjustment Terakhir</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="20%">Waktu</th>
                        <th>Aksi</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory->adjustments as $adj)
                    <tr>
                        <td class="small text-muted">{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($adj->status === 'plus')
                                <span class="badge bg-soft-success text-success">
                                    <i class="fas fa-arrow-up me-1"></i> MASUK
                                </span>
                            @else
                                <span class="badge bg-soft-danger text-danger">
                                    <i class="fas fa-arrow-down me-1"></i> KELUAR
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold">
                            {{ $adj->status === 'plus' ? '+' : '-' }}{{ number_format($adj->quantity) }}
                        </td>
                        <td class="small">{{ $adj->note ?? '-' }}</td>
                        <td class="small">{{ $adj->user->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pergerakan stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
