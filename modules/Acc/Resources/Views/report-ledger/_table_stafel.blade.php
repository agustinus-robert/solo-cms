<table class="table table-bordered table-hover">
    <thead class="table-light text-center align-middle">
        <tr>
            <th width="12%">Tanggal</th>
            <th width="33%">Keterangan</th>
            <th width="10%">Ref</th>
            <th width="15%">Debet</th>
            <th width="15%">Kredit</th>
            <th width="15%">Saldo Berjalan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $bal = (float)$report['initial_balance'];
            $isDebitNormal = $report['coa']->normal_balance->value === 'debit';
        @endphp

        <!-- Baris Saldo Awal -->
        <tr class="bg-light">
            <td class="text-center">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
            <td colspan="2" class="fw-bold">SALDO AWAL</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-end fw-bold {{ $bal < 0 ? 'text-danger' : '' }}">
                {{ number_format($bal, 2) }}
            </td>
        </tr>

        @forelse($report['mutations'] as $row)
            @php
                // Logika Saldo Berjalan sesuai Normal Balance
                if ($isDebitNormal) {
                    $bal += ($row->debit - $row->credit);
                } else {
                    $bal += ($row->credit - $row->debit);
                }
            @endphp
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->transaction_date)->format('d/m/y') }}</td>
                <td>{{ $row->description }}</td>
                <td class="text-center"><code>{{ $row->reference_number }}</code></td>
                <td class="text-end text-success">
                    {{ $row->debit > 0 ? number_format($row->debit, 2) : '-' }}
                </td>
                <td class="text-end text-danger">
                    {{ $row->credit > 0 ? number_format($row->credit, 2) : '-' }}
                </td>
                <td class="text-end fw-bold {{ ($bal < 0) ? 'text-danger' : 'text-primary' }}">
                    {{ number_format($bal, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    Tidak ada mutasi transaksi untuk periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot class="table-light">
        <tr>
            <td colspan="5" class="text-end fw-bold">SALDO AKHIR</td>
            <td class="text-end fw-bold bg-yellow-100 {{ $bal < 0 ? 'text-danger' : '' }}">
                {{ number_format($bal, 2) }}
            </td>
        </tr>
    </tfoot>
</table>
