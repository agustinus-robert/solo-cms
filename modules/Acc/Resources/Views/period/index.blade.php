@extends('acc::layouts.default')

@section('title', 'Periode Akuntansi | ')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Periode Akuntansi</h5>
        <a href="{{ route('acc::period.create') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-plus me-1"></i> Tambah Periode
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('acc::period.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama periode..." value="{{ request('search') }}">
                    <button class="btn btn-light border" type="submit">Cari</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            @include('acc::period._table')
        </div>

        <div class="mt-4">
            {{ $periods->links() }}
        </div>
    </div>
</div>
@endsection
