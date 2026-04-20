@extends('hotel::layouts.default')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('hotel::room.index') }}" class="btn btn-light border me-3"><i class="mdi mdi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-0">Inventaris Kamar {{ $room->room_number }}</h4>
                    <p class="text-muted small mb-0">Status: {{ strtoupper($room->status->name ?? 'UNKNOWN') }}</p>
                </div>
            </div>
            <a href="{{ route('hotel::room-inventory.upsert', $room->id) }}" class="btn btn-primary">Atur Inventaris</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @include('hotel::room-inventory._table-view')
            </div>
        </div>
    </div>
</div>
@endsection
