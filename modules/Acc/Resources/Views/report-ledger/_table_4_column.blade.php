<table class="table table-bordered table-hover" style="min-width: 1000px;">
    <thead class="table-light text-center align-middle">
        <tr>
            <th rowspan="2" width="10%">Tanggal</th>
            <th rowspan="2" width="25%">Keterangan</th>
            <th rowspan="2" width="10%">Ref</th>
            <th rowspan="2" width="12%">Debet</th>
            <th rowspan="2" width="12%">Kredit</th>
            <th colspan="2" width="24%">Saldo</th>
        </tr>
        <tr>
            <th width="12%">Debet</th>
            <th width="12%">Kredit</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentBalance = (float)$report['initial_balance'];
            $isDebitNormal = $report['coa']->normal_balance->value === 'debit';
        @endphp

        <!-- Baris Saldo Awal -->
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
            <td colspan="2" class="fw-bold text-center">SALDO AWAL</td>
            <td class="bg-light"></td>
            <td class="bg-light"></td>

            {{-- Posisi Saldo Awal --}}
            @if($isDebitNormal)
                <td class="text-end fw-bold">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
                <td class="text-end fw-bold">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
            @else
                <td class="text-end fw-bold">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
                <td class="text-end fw-bold">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
            @endif
        </tr>

        @forelse($report['mutations'] as $row)
            @php
                // Logika Matematika: Jika normal debit, maka tambah jika ketemu debit, kurang jika ketemu kredit.
                if ($isDebitNormal) {
                    $currentBalance += ((float)$row->debit - (float)$row->credit);
                } else {
                    $currentBalance += ((float)$row->credit - (float)$row->debit);
                }
            @endphp
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->transaction_date)->format('d/m/y') }}</td>
                <td>{{ $row->description }}</td>
                <td class="text-center"><code>{{ $row->reference_number }}</code></td>
                <td class="text-end text-success">{{ $row->debit > 0 ? number_format($row->debit, 2) : '-' }}</td>
                <td class="text-end text-danger">{{ $row->credit > 0 ? number_format($row->credit, 2) : '-' }}</td>

                {{-- Penempatan Saldo Akhir (Real-time) --}}
                @if($isDebitNormal)
                    {{-- Jika Akun Aset/Beban: Saldo positif di kolom Debet, Saldo negatif di kolom Kredit --}}
                    <td class="text-end fw-bold text-primary">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
                    <td class="text-end fw-bold text-danger">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
                @else
                    {{-- Jika Akun Hutang/Modal/Pendapatan: Saldo positif di kolom Kredit, Saldo negatif di kolom Debet --}}
                    <td class="text-end fw-bold text-danger">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
                    <td class="text-end fw-bold text-primary">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi untuk periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot class="table-light">
        <tr>
            <td colspan="5" class="text-end fw-bold">SALDO AKHIR PERIODE</td>
            @if($isDebitNormal)
                <td class="text-end fw-bold bg-yellow-100">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
                <td class="text-end fw-bold bg-yellow-100">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
            @else
                <td class="text-end fw-bold bg-yellow-100">{{ $currentBalance < 0 ? number_format(abs($currentBalance), 2) : '-' }}</td>
                <td class="text-end fw-bold bg-yellow-100">{{ $currentBalance >= 0 ? number_format($currentBalance, 2) : '-' }}</td>
            @endif
        </tr>
    </tfoot>
</table>
