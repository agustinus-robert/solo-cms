<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4">Tanggal</th>
                <th>Referensi</th>
                <th>Keterangan</th>
                <th>Modul</th>
                <th class="text-end">Nominal (Debit)</th>
                <th class="text-end pe-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $ledger)
                <tr>
                    <td class="ps-4">{{ \Carbon\Carbon::parse($ledger->transaction_date)->format('d/m/Y') }}</td>
                    <td><span class="badge bg-soft-primary text-primary fw-bold">{{ $ledger->reference_number }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($ledger->description, 50) }}</td>
                    <td>
                        <span class="badge bg-outline-info text-info border border-info px-2">
                            {{ strtoupper($ledger->source_module) }}
                        </span>
                    </td>
                    <td class="text-end fw-bold">
                        Rp {{ number_format($ledger->ledgerEntries->sum('debit'), 0, ',', '.') }}
                    </td>
                    <td class="text-end pe-4">
                        <a href="" class="btn btn-sm btn-primary shadow-sm px-3">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi jurnal terdeteksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
