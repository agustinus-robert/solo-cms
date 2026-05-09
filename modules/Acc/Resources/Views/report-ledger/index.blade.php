@extends('acc::layouts.default')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Buku Besar (General Ledger)</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('acc::report.ledger') }}" class="row g-3 mb-4 border-bottom pb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rekening / Akun</label>
                    <select name="coa_id" class="form-select select2" required>
                        <option value="">-- Pilih Rekening --</option>
                        @foreach($coas as $c)
                            <option value="{{ $c->id }}" {{ $coaId == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tampilan</label>
                    <select name="view_type" class="form-select">
                        <option value="stafel" {{ $viewType == 'stafel' ? 'selected' : '' }}>Stafel (Saldo Berjalan)</option>
                        <option value="skrontro" {{ $viewType == 'skrontro' ? 'selected' : '' }}>Skrontro (2 Kolom)</option>
                        <option value="4kolom" {{ $viewType == '4kolom' ? 'selected' : '' }}>4 Kolom (D/K Saldo)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Dari</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sampai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </form>

            @if(isset($report) && $report)
                <div class="text-center mb-3">
                    <h3>{{ $report['coa']->name }}</h3>
                    <h5>Kode: {{ $report['coa']->code }}</h5>
                    <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                </div>

                <div class="table-responsive" style="overflow-x: auto;">
                    @if($viewType == 'skrontro')
                        @include('acc::report-ledger._table_skrontro')
                    @elseif($viewType == '4kolom')
                        @include('acc::report-ledger._table_4_column')
                    @else
                        @include('acc::report-ledger._table_stafel')
                    @endif
                </div>
            @else
                <hr>
                <div class="text-center py-5">
                    <!-- Icon Utama -->
                    <div class="mb-4">
                        <div class="display-1 text-light">
                            <i class="mdi mdi-book-search-outline"></i>
                        </div>
                    </div>

                    <!-- Judul -->
                    <h4 class="text-muted fw-normal">Pilih Filter dahulu!</h4>

                    <!-- Deskripsi -->
                    <p class="text-muted mx-auto" style="max-width: 450px;">
                        Silahkan tentukan <strong>Rekening</strong> dan <strong>Periode Tanggal</strong> pada filter di atas untuk menarik data mutasi dari buku besar.
                    </p>

                    <!-- Elemen Tambahan: Icon Panah ke Atas -->
                    <div class="mt-4">
                        <i class="mdi mdi-arrow-up-bold-outline mdi-24px text-primary animate-bounce"></i>
                    </div>
                </div>

                <style>
                    /* Efek gerak naik turun sedikit untuk icon panah */
                    .animate-bounce {
                        animation: bounce 2s infinite;
                    }

                    @keyframes bounce {
                        0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
                        40% {transform: translateY(-10px);}
                        60% {transform: translateY(-5px);}
                    }
                </style>
            @endif
        </div>
    </div>
</div>
@endsection
