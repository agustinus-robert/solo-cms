@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Neraca Lajur (10 Kolom) - {{ $period->name }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle" style="font-size: 0.8rem;">
                <thead class="table-dark text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">Kode</th>
                        <th rowspan="2" class="align-middle">Nama Akun</th>
                        <th colspan="2">Neraca Saldo</th>
                        <th colspan="2">Penyesuaian</th>
                        <th colspan="2">NS Disesuaikan</th>
                        <th colspan="2">Laba Rugi</th>
                        <th colspan="2">Neraca</th>
                    </tr>
                    <tr>
                        <th>D</th><th>K</th>
                        <th>D</th><th>K</th>
                        <th>D</th><th>K</th>
                        <th>D</th><th>K</th>
                        <th>D</th><th>K</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = array_fill_keys(['nsd','adj','nsd_d','nsd_k','lr_d','lr_k','nr_d','nr_k'], 0);
                    @endphp
                    @foreach($worksheet as $row)
                        <tr>
                            <td class="text-center">{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>

                            {{-- NS --}}
                            <td class="text-end">{{ number_format($row->ns['d'], 0) }}</td>
                            <td class="text-end">{{ number_format($row->ns['k'], 0) }}</td>

                            {{-- Adj --}}
                            <td class="text-end text-primary">{{ $row->adj['d'] > 0 ? number_format($row->adj['d'], 0) : '-' }}</td>
                            <td class="text-end text-primary">{{ $row->adj['k'] > 0 ? number_format($row->adj['k'], 0) : '-' }}</td>

                            {{-- NSD --}}
                            <td class="text-end fw-bold">{{ $row->nsd['d'] > 0 ? number_format($row->nsd['d'], 0) : '-' }}</td>
                            <td class="text-end fw-bold">{{ $row->nsd['k'] > 0 ? number_format($row->nsd['k'], 0) : '-' }}</td>

                            {{-- Laba Rugi --}}
                            <td class="text-end bg-light">{{ $row->lr['d'] > 0 ? number_format($row->lr['d'], 0) : '-' }}</td>
                            <td class="text-end bg-light">{{ $row->lr['k'] > 0 ? number_format($row->lr['k'], 0) : '-' }}</td>

                            {{-- Neraca --}}
                            <td class="text-end">{{ $row->nr['d'] > 0 ? number_format($row->nr['d'], 0) : '-' }}</td>
                            <td class="text-end">{{ $row->nr['k'] > 0 ? number_format($row->nr['k'], 0) : '-' }}</td>
                        </tr>
                        @php
                            $totals['lr_d'] += $row->lr['d']; $totals['lr_k'] += $row->lr['k'];
                            $totals['nr_d'] += $row->nr['d']; $totals['nr_k'] += $row->nr['k'];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    {{-- Baris Selisih Laba/Rugi --}}
                    @php
                        $labaRugi = $totals['lr_k'] - $totals['lr_d'];
                    @endphp
                    <tr>
                        <td colspan="8" class="text-end">LABA / (RUGI) BERSIH</td>
                        <td class="text-end text-danger">{{ $labaRugi < 0 ? number_format(abs($labaRugi), 0) : '-' }}</td>
                        <td class="text-end text-success">{{ $labaRugi >= 0 ? number_format($labaRugi, 0) : '-' }}</td>
                        <td class="text-end text-success">{{ $labaRugi >= 0 ? number_format($labaRugi, 0) : '-' }}</td>
                        <td class="text-end text-danger">{{ $labaRugi < 0 ? number_format(abs($labaRugi), 0) : '-' }}</td>
                    </tr>
                    <tr class="table-dark">
                        <td colspan="8" class="text-end">TOTAL BALANCE</td>
                        <td class="text-end">{{ number_format(max($totals['lr_d'], $totals['lr_k']), 0) }}</td>
                        <td class="text-end">{{ number_format(max($totals['lr_d'], $totals['lr_k']), 0) }}</td>
                        <td class="text-end">{{ number_format(max($totals['nr_d'], $totals['nr_k']), 0) }}</td>
                        <td class="text-end">{{ number_format(max($totals['nr_d'], $totals['nr_k']), 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
