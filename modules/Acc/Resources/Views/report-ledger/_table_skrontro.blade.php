<table class="table table-bordered table-sm" style="min-width: 1000px;">
    <thead class="table-light text-center align-middle">
        <tr>
            <th colspan="4" class="bg-success text-white">DEBET</th>
            <th colspan="4" class="bg-danger text-white">KREDIT</th>
        </tr>
        <tr>
            <th width="10%">Tanggal</th>
            <th width="20%">Keterangan</th>
            <th width="10%">Ref</th>
            <th width="10%">Jumlah</th>
            <th width="10%">Tanggal</th>
            <th width="20%">Keterangan</th>
            <th width="10%">Ref</th>
            <th width="10%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php
            $isDebitNormal = $report['coa']->normal_balance->value == 'debit';

            // Siapkan collection debit dan kredit secara terpisah
            $debitList = collect();
            $creditList = collect();

            // 1. Masukkan Saldo Awal ke list yang sesuai
            if ($report['initial_balance'] != 0) {
                $itemAwal = (object)[
                    'date' => \Carbon\Carbon::parse($startDate)->format('d/m/y'),
                    'desc' => 'SALDO AWAL',
                    'ref'  => '-',
                    'amt'  => number_format(abs($report['initial_balance']), 2)
                ];

                if ($isDebitNormal) {
                    $debitList->push($itemAwal);
                } else {
                    $creditList->push($itemAwal);
                }
            }

            // 2. Masukkan Mutasi ke masing-masing list
            foreach ($report['mutations'] as $m) {
                if ($m->debit > 0) {
                    $debitList->push((object)[
                        'date' => \Carbon\Carbon::parse($m->transaction_date)->format('d/m/y'),
                        'desc' => $m->description,
                        'ref'  => $m->reference_number,
                        'amt'  => number_format($m->debit, 2)
                    ]);
                }
                if ($m->credit > 0) {
                    $creditList->push((object)[
                        'date' => \Carbon\Carbon::parse($m->transaction_date)->format('d/m/y'),
                        'desc' => $m->description,
                        'ref'  => $m->reference_number,
                        'amt'  => number_format($m->credit, 2)
                    ]);
                }
            }

            // 3. Tentukan berapa baris maksimal yang akan dirender
            $maxRows = max($debitList->count(), $creditList->count());
        @endphp

        @for($i = 0; $i < $maxRows; $i++)
            @php
                $d = $debitList->get($i);
                $k = $creditList->get($i);
            @endphp
            <tr>
                <!-- Sisi Debet -->
                <td class="text-center">{{ $d->date ?? '' }}</td>
                <td>{{ $d->desc ?? '' }}</td>
                <td class="text-center"><code>{{ $d->ref ?? '' }}</code></td>
                <td class="text-end text-success">{{ $d->amt ?? '' }}</td>

                <!-- Sisi Kredit -->
                <td class="text-center">{{ $k->date ?? '' }}</td>
                <td>{{ $k->desc ?? '' }}</td>
                <td class="text-center"><code>{{ $k->ref ?? '' }}</code></td>
                <td class="text-end text-danger">{{ $k->amt ?? '' }}</td>
            </tr>
        @endfor

        @if($maxRows == 0)
            <tr>
                <td colspan="8" class="text-center py-3 text-muted italic">Tidak ada mutasi pada periode ini.</td>
            </tr>
        @endif
    </tbody>
    <tfoot class="table-light fw-bold">
        @php
            $totalD = $report['mutations']->sum('debit') + ($isDebitNormal ? $report['initial_balance'] : 0);
            $totalK = $report['mutations']->sum('credit') + (!$isDebitNormal ? $report['initial_balance'] : 0);
        @endphp
        <tr>
            <td colspan="3" class="text-end">TOTAL DEBET</td>
            <td class="text-end">{{ number_format($totalD, 2) }}</td>
            <td colspan="3" class="text-end">TOTAL KREDIT</td>
            <td class="text-end">{{ number_format($totalK, 2) }}</td>
        </tr>
    </tfoot>
</table>
