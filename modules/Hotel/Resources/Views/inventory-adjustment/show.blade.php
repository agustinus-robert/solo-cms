@extends('hotel::layouts.default')

@section('title', 'Inventory Adjustment '.$inventory->name.' | ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('hotel::inventory.index') }}" class="btn btn-light border shadow-sm me-3">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <h4 class="mb-0">Detail Adjustment</h4>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('hotel::inventory.index') }}">Inventory</a></li>
                        <li class="breadcrumb-item active">Adjustment</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h4 class="card-title text-primary mb-0">{{ $inventory->name }}</h4>
                        <span class="badge {{ $inventory->type_badge }}">{{ $inventory->type->name }}</span>
                    </div>
                    <hr>

                    <div class="text-center py-3">
                        <p class="text-muted mb-1">Stok Saat Ini</p>
                        <h1 class="display-4 fw-bold {{ $inventory->current_stock < $inventory->min_stock ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($inventory->current_stock) }}
                        </h1>
                        <span class="badge bg-light text-dark border">{{ $inventory->unit }}</span>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdjustment">
                            <i class="fas fa-plus-minus me-1"></i> Input Adjustment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            @include('hotel::inventory-adjustment._table')
        </div>
    </div>
</div>

@include('hotel::inventory-adjustment._modal')

@endsection
