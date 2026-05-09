@extends('acc::layouts.default')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Jurnal Umum</h5>
        <a href="{{ route('acc::ledger.create') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-plus me-1"></i> Buat Jurnal Manual
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('acc::ledger.index') }}" method="GET" class="row g-2 mb-4">
            <!-- Filter Search -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="No. Ref / Deskripsi..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Filter Tipe Jurnal -->
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    @foreach(\Modules\Acc\Enums\LedgerType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a href="{{ route('acc::ledger.index') }}" class="btn btn-light border">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            @include('acc::ledger._table')
        </div>

        <div class="mt-4">
            {{ $ledgers->links() }}
        </div>
    </div>
</div>
@endsection
