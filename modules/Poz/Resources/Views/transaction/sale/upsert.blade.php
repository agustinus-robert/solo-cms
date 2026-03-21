@php
    $isPos = request()->query('pos') === 'true';
@endphp

@push('styles')
    @if($isPos)
        @include('poz::transaction.sale.partials.pos-style')
    @endif
@endpush

@if($isPos)
    <script>document.body.classList.add('pos-mode');</script>
@endif

<div class="{{ $isPos ? 'pos-container' : 'container-fluid py-4' }}">

    @include('poz::transaction.sale.partials.header')

    <form id="saleForm" action="{{ route('poz::transaction.sale.store') }}" method="POST">
        @csrf
        @if($sale) <input type="hidden" name="id" value="{{ $sale->id }}"> @endif
        <input type="hidden" name="items" id="itemsInput">
        <input type="hidden" name="outlet_id" value="{{ $outletId }}">

        @if($isPos)
            @include('poz::transaction.sale.partials.pos')
        @else
            <div class="row g-4 mt-2">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 text-dark">
                        @include('poz::transaction.sale.partials.search')
                        <div class="mt-3">
                            @include('poz::transaction.sale.partials.table')
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('poz::transaction.sale.partials.summary')
                </div>
            </div>
        @endif
    </form>
</div>

@push('scripts')
    @if(!$isPos)
        @include('poz::transaction.sale.partials.scripts')
    @endif
@endpush
