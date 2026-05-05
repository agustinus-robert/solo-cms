@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h4 class="text-center fw-bold text-uppercase mb-1">Neraca (Balance Sheet)</h4>
        <p class="text-center text-muted mb-4">Periode: {{ $periods->where('id', $selectedPeriodId)->first()?->name }}</p>

        <div class="row">
            <!-- KOLOM KIRI: ASET -->
            <div class="col-md-6 border-end">
                <h6 class="fw-bold text-primary border-bottom pb-2">AKTIVA (ASSETS)</h6>
                <table class="table table-sm table-borderless">
                    @php $totalAsset = 0; @endphp
                    @foreach($report->get('asset', []) as $row)
                        @php $totalAsset += $row->total; @endphp
                        <tr>
                            <td>{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold border-top">
                        <td>TOTAL AKTIVA</td>
                        <td class="text-end text-primary">{{ number_format($totalAsset, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- KOLOM KANAN: PASIVA -->
            <div class="col-md-6">
                <h6 class="fw-bold text-danger border-bottom pb-2">KEWAJIBAN (LIABILITIES)</h6>
                <table class="table table-sm table-borderless">
                    @php $totalLiability = 0; @endphp
                    @foreach($report->get('liability', []) as $row)
                        @php $totalLiability += $row->total; @endphp
                        <tr>
                            <td>{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold border-top">
                        <td>TOTAL KEWAJIBAN</td>
                        <td class="text-end">{{ number_format($totalLiability, 2) }}</td>
                    </tr>
                </table>

                <h6 class="fw-bold text-success border-bottom pb-2 mt-4">EKUITAS (EQUITY)</h6>
                <table class="table table-sm table-borderless">
                    @php $totalEquity = 0; @endphp
                    @foreach($report->get('equity', []) as $row)
                        @php $totalEquity += $row->total; @endphp
                        <tr>
                            <td>{{ $row->code }} - {{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <!-- LABA BERJALAN DARI PROFIT LOSS -->
                    <tr>
                        <td class="fst-italic ps-2">Laba (Rugi) Periode Berjalan</td>
                        <td class="text-end">{{ number_format($netProfit, 2) }}</td>
                    </tr>
                    @php $totalEquity += $netProfit; @endphp
                    <tr class="fw-bold border-top">
                        <td>TOTAL EKUITAS</td>
                        <td class="text-end">{{ number_format($totalEquity, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- FOOTER VALIDASI -->
        <div class="row mt-4 border-top pt-3">
            <div class="col-md-6 bg-light p-3">
                <h5 class="mb-0">TOTAL AKTIVA: <span class="float-end text-primary">{{ number_format($totalAsset, 2) }}</span></h5>
            </div>
            <div class="col-md-6 bg-light p-3">
                <h5 class="mb-0">TOTAL PASIVA: <span class="float-end text-success">{{ number_format($totalLiability + $totalEquity, 2) }}</span></h5>
            </div>
        </div>
    </div>
</div>
@endsection
