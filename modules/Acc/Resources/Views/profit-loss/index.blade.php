@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Laporan Laba Rugi</h5>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-print-none">Cetak PDF</button>
    </div>
    <div class="card-body">
        <form action="{{ route('acc::profit-loss') }}" method="GET" class="row g-3 mb-4 d-print-none">
            <div class="col-md-4">
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="report-content">
            <h4 class="text-center mb-4 text-uppercase">Laporan Laba Rugi</h4>

            <!-- SECTION PENDAPATAN -->
            <table class="table table-borderless">
                <thead class="border-bottom">
                    <tr><th colspan="2" class="text-primary">PENDAPATAN</th></tr>
                </thead>
                <tbody>
                    @php $totalRevenue = 0; @endphp
                    @foreach($report->get('revenue', []) as $row)
                        @php $totalRevenue += $row->total; @endphp
                        <tr>
                            <td>{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold border-top">
                        <td>TOTAL PENDAPATAN</td>
                        <td class="text-end text-decoration-underline">{{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tbody>

                <!-- SPACE -->
                <tr><td colspan="2">&nbsp;</td></tr>

                <!-- SECTION BEBAN -->
                <thead class="border-bottom">
                    <tr><th colspan="2" class="text-danger">BEBAN / BIAYA</th></tr>
                </thead>
                <tbody>
                    @php $totalExpense = 0; @endphp
                    @foreach($report->get('expense', []) as $row)
                        @php $totalExpense += $row->total; @endphp
                        <tr>
                            <td>{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">({{ number_format($row->total, 2) }})</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold border-top">
                        <td>TOTAL BEBAN</td>
                        <td class="text-end text-decoration-underline text-danger">({{ number_format($totalExpense, 2) }})</td>
                    </tr>
                </tbody>

                <!-- FINAL RESULT -->
                <tfoot>
                    <tr class="fs-5 fw-bold bg-light">
                        <td class="py-3">LABA / (RUGI) BERSIH</td>
                        <td class="text-end py-3 {{ ($totalRevenue - $totalExpense) < 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($totalRevenue - $totalExpense, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
