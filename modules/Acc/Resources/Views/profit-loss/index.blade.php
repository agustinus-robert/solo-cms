@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Laporan Laba Rugi</h5>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-print-none">
            <i class="mdi mdi-printer"></i> Cetak PDF
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('acc::profit-loss') }}" method="GET" class="row g-3 mb-4 d-print-none">
            <div class="col-md-4">
                <label class="small fw-bold">Pilih Periode</label>
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="report-content p-4">
            <div class="text-center mb-5">
                <h3 class="fw-bold mb-1">LAPORAN LABA RUGI</h3>
                <p class="text-muted">Periode: {{ $periods->where('id', $selectedPeriodId)->first()?->name }}</p>
            </div>

            <table class="table table-borderless align-middle">
                <!-- SECTION PENDAPATAN -->
                <thead class="border-bottom">
                    <tr><th colspan="2" class="text-primary pb-2">PENDAPATAN</th></tr>
                </thead>
                <tbody>
                    @php $totalRevenue = 0; @endphp
                    @forelse($report->get('revenue', []) as $row)
                        @php $totalRevenue += $row->total; @endphp
                        <tr>
                            <td class="ps-3">{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted small">Tidak ada data pendapatan</td></tr>
                    @endforelse
                    <tr class="fw-bold border-top">
                        <td class="py-3">TOTAL PENDAPATAN</td>
                        <td class="text-end py-3 text-decoration-underline">{{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tbody>

                <!-- SPACE -->
                <tr><td colspan="2" class="py-3">&nbsp;</td></tr>

                <!-- SECTION BEBAN -->
                <thead class="border-bottom">
                    <tr><th colspan="2" class="text-danger pb-2">BEBAN / BIAYA</th></tr>
                </thead>
                <tbody>
                    @php $totalExpense = 0; @endphp
                    @forelse($report->get('expense', []) as $row)
                        @php $totalExpense += $row->total; @endphp
                        <tr>
                            <td class="ps-3">{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">({{ number_format(abs($row->total), 2) }})</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted small">Tidak ada data beban</td></tr>
                    @endforelse
                    <tr class="fw-bold border-top">
                        <td class="py-3">TOTAL BEBAN</td>
                        <td class="text-end py-3 text-danger text-decoration-underline">({{ number_format(abs($totalExpense), 2) }})</td>
                    </tr>
                </tbody>

                <!-- FINAL RESULT -->
                <tfoot>
                    @php $netProfit = $totalRevenue - $totalExpense; @endphp
                    <tr class="fs-5 fw-bold bg-light border-top border-dark">
                        <td class="py-3 ps-2">LABA / (RUGI) BERSIH</td>
                        <td class="text-end py-3 pe-2 {{ $netProfit < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $netProfit < 0 ? '(' . number_format(abs($netProfit), 2) . ')' : number_format($netProfit, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
