@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Trial Balance (Neraca Saldo)</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('acc::trial-balance') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Periode</label>
                <select name="period_id" class="form-select" onchange="this.form.submit()">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th rowspan="2">Kode Akun</th>
                        <th rowspan="2">Nama Akun</th>
                        <th rowspan="2">Saldo Awal</th>
                        <th colspan="2">Mutasi</th>
                        <th rowspan="2">Saldo Akhir</th>
                    </tr>
                    <tr class="text-center">
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp
                    @foreach($data as $row)
                        @php
                            $totalDebit += $row->total_debit;
                            $totalCredit += $row->total_credit;
                        @endphp
                        <tr>
                            <td class="font-monospace">{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>
                            <td class="text-end">{{ number_format($row->beginning_balance, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($row->total_debit, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row->total_credit, 2) }}</td>
                            <td class="text-end fw-bold {{ $row->ending_balance < 0 ? 'text-danger' : '' }}">
                                {{ number_format($row->ending_balance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL MUTASI</td>
                        <td class="text-end text-success">{{ number_format($totalDebit, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($totalCredit, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
