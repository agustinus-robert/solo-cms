<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th width="120">Tanggal</th>
            <th width="150">Referensi</th>
            <th>Akun & Deskripsi</th>
            <th width="150" class="text-end">Debit</th>
            <th width="150" class="text-end">Kredit</th>
            <th width="80" class="text-center">#</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ledgers as $ledger)
            <!-- Header Transaksi -->
            <tr class="table-secondary">
                <td class="fw-bold">{{ $ledger->transaction_date }}</td>
                <td><span class="badge bg-dark">{{ $ledger->reference_number }}</span></td>
                <td class="fw-bold text-uppercase">{{ $ledger->description ?? '-' }}</td>
                <td colspan="2"></td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border shadow-sm" data-bs-toggle="dropdown">
                            <i class="mdi mdi-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('acc::ledger.edit', $ledger->id) }}">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0)"
                                   onclick="if(confirm('Hapus Jurnal?')) {
                                       fetch('{{ route('acc::ledger.destroy', $ledger->id) }}', {
                                           method: 'DELETE',
                                           headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                       }).then(() => location.reload())
                                   }">Hapus</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <!-- Baris Entri -->
            @foreach($ledger->ledgerEntries as $entry)
                <tr>
                    <td colspan="2" class="border-0"></td>
                    <td class="border-start-0">
                        <div class="{{ $entry->credit > 0 ? 'ps-5 text-muted' : 'fw-semibold' }}">
                            {{ $entry->coa->code }} - {{ $entry->coa->name }}
                        </div>
                    </td>
                    <td class="text-end border-start-0 text-success">
                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                    </td>
                    <td class="text-end border-start-0 text-danger">
                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                    </td>
                    <td class="border-start-0"></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">Kosong, belum ada transaksi.</td>
            </tr>
        @endforelse
    </tbody>
</table>
