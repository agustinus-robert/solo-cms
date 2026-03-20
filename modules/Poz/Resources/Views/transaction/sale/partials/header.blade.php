@php
    $modePos = request()->query('pos') === 'true';
    $urlRegular = request()->fullUrlWithQuery(['pos' => null]);
    $urlPos = request()->fullUrlWithQuery(['pos' => 'true']);
@endphp

<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-3">
        <h4 class="fw-bold mb-0 text-dark">{{ $action ?? 'Create' }} Penjualan</h4>

        <div class="btn-group p-1 bg-light rounded-pill border" style="border-color: #dee2e6 !important;">
            @if(!$modePos)
                <a href="{{ $urlRegular }}" class="btn btn-sm rounded-pill px-3 bg-white shadow-sm fw-bold border text-primary">
                    <i class="fa fa-list-ul me-1"></i> Regular
                </a>
                <a href="{{ $urlPos }}" class="btn btn-sm rounded-pill px-3 text-secondary border-0">
                    <i class="fa fa-th-large me-1"></i> POS Mode
                </a>
            @else
                <a href="{{ $urlRegular }}" class="btn btn-sm rounded-pill px-3 text-secondary border-0">
                    <i class="fa fa-list-ul me-1"></i> Regular
                </a>
                <a href="{{ $urlPos }}" class="btn btn-sm rounded-pill px-3 bg-white shadow-sm fw-bold border text-primary">
                    <i class="fa fa-th-large me-1"></i> POS Mode
                </a>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        @if($modePos)
            <button type="button" class="btn btn-sm btn-light border shadow-sm" onclick="toggleFullScreen()">
                <i class="fa fa-expand"></i>
            </button>
        @endif
        <a href="{{ route('poz::transaction.sale.index') }}" class="btn btn-sm btn-outline-secondary px-3 shadow-sm">
            <i class="fa fa-times me-1"></i> Batal
        </a>
    </div>
</div>
