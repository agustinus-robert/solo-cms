<table class="table table-bordered align-middle shadow-sm">
    <thead class="table-dark text-center">
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
            @php
                // Tentukan warna berdasarkan Enum LedgerType
                $rowClass = match($ledger->type->value) {
                    'adjustment' => 'table-warning', // Kuning untuk Penyesuaian
                    'closing'    => 'table-danger',  // Merah muda untuk Penutup
                    default      => 'table-light',   // Putih/Abu tipis untuk Umum
                };

                $badgeClass = match($ledger->type->value) {
                    'adjustment' => 'bg-warning text-dark',
                    'closing'    => 'bg-danger',
                    default      => 'bg-primary',
                };
            @endphp

            <!-- Header Transaksi -->
            <tr class="{{ $rowClass }}">
                <td class="fw-bold text-center">{{ \Carbon\Carbon::parse($ledger->transaction_date)->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $badgeClass }} mb-1">{{ $ledger->type->label() }}</span><br>
                    <small class="fw-bold text-muted">{{ $ledger->reference_number }}</small>
                </td>
                <td>
                    <div class="fw-bold text-uppercase">{{ $ledger->description ?? '-' }}</div>
                    @if($ledger->source_module !== 'manual')
                        <small class="text-muted small">Source: {{ strtoupper($ledger->source_module) }}</small>
                    @endif
                </td>
                <td colspan="2"></td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border shadow-sm" data-bs-toggle="dropdown">
                            <i class="mdi mdi-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('acc::ledger.edit', $ledger->id) }}"><i class="mdi mdi-pencil me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0)"
                                   onclick="if(confirm('Hapus Jurnal?')) {
                                       fetch('{{ route('acc::ledger.destroy', $ledger->id) }}', {
                                           method: 'DELETE',
                                           headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                       }).then(() => location.reload())
                                   }"><i class="mdi mdi-trash-can me-2"></i>Hapus</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>

            <!-- Baris Entri -->
            @foreach($ledger->ledgerEntries as $entry)
                <tr>
                    <td colspan="2" class="border-0"></td>
                    <td class="border-start-0 py-1">
                        <div class="{{ $entry->credit > 0 ? 'ps-5 text-muted' : 'fw-semibold text-dark' }}">
                            {{ $entry->coa->code }} - {{ $entry->coa->name }}
                        </div>
                    </td>
                    <td class="text-end border-start-0 text-success py-1">
                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                    </td>
                    <td class="text-end border-start-0 text-danger py-1">
                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                    </td>
                    <td class="border-start-0"></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted italic">
                    <i class="mdi mdi-database-off mdi-48px d-block mb-2"></i>
                    Belum ada transaksi jurnal yang ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
