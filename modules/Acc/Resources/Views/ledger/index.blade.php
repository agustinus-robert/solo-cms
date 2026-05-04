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
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="No. Ref / Deskripsi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-light border" type="submit">Filter</button>
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
