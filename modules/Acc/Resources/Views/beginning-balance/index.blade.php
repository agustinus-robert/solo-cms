@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Setting Saldo Awal</h5>
    </div>
    <div class="card-body">
        <!-- Filter Periode -->
        <form action="{{ route('acc::beginning-balance.index') }}" method="GET" class="row g-3 mb-4" id="periodForm">
            <div class="col-md-4">
                <label class="form-label fw-bold">Pilih Periode Akuntansi</label>
                <select name="period_id" class="form-select select2" onchange="this.form.submit()">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} {{ $p->is_closed ? '(Laporan Ditutup)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Form Input Saldo -->
        <form action="{{ route('acc::beginning-balance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="period_id" value="{{ $selectedPeriodId }}">

            <div class="table-responsive">
                @include('acc::beginning-balance._table')
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('acc::period.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="mdi mdi-content-save me-1"></i> Simpan Saldo Awal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
