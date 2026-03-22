@extends('web::electro.index')
@section('title', "Status Pesanan #$sale->reference")

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-5 text-center">

                    @if($sale->sale_status == 2)
                        <div class="mb-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                                <i class="fas fa-check fa-2x"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark">Pembayaran Berhasil!</h3>
                        <p class="text-muted">Pesanan #{{ $sale->reference }} sedang disiapkan.</p>
                        <hr class="my-4 opacity-0">
                        <a href="/" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">LANJUT BELANJA</a>

                    @elseif($sale->sale_status == 3)
                        <div class="mb-4">
                            <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                                <i class="fas fa-times fa-2x"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark">Transaksi Gagal</h3>
                        <p class="text-muted">Batas waktu pembayaran habis atau transaksi dibatalkan.</p>
                        <a href="{{ route('web::web.cart.detail') }}" class="btn btn-outline-danger w-100 py-3 fw-bold rounded-pill">COBA LAGI</a>

                    @else
                        <div class="mb-4">
                            <div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Selesaikan Pembayaran</h4>
                        <p class="text-muted small">Pesanan #{{ $sale->reference }}</p>

                        <div class="bg-light p-4 rounded-4 border border-dashed my-4">
                            <p class="extra-small text-uppercase fw-bold text-muted mb-2">Virtual Account {{ $sale->midtrans->payment_type }}</p>
                            <h2 class="text-primary fw-bold mb-2" id="va-num">{{ $sale->midtrans->va_number }}</h2>
                            <button class="btn btn-sm btn-outline-primary px-3 rounded-pill" onclick="copyVA()">
                                <i class="fas fa-copy me-1"></i> Salin Nomor
                            </button>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6 text-start">
                                <small class="text-muted d-block">Total Bayar</small>
                                <strong class="text-dark">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">Batas Waktu</small>
                                <strong class="text-danger small">{{ $sale->midtrans->expiry_time->format('d M, H:i') }} WIB</strong>
                            </div>
                        </div>

                        <a href="/profile/orders" class="btn btn-dark w-100 py-3 fw-bold rounded-pill shadow">CEK STATUS PESANAN</a>
                        <p class="extra-small text-muted mt-3 italic">*Halaman ini akan otomatis berubah setelah Anda membayar.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyVA() {
        var el = document.getElementById("va-num");
        navigator.clipboard.writeText(el.innerText);
        alert("Nomor Virtual Account berhasil disalin!");
    }
</script>

<style>
    .rounded-4 { border-radius: 1.5rem; }
    .border-dashed { border-style: dashed !important; border-width: 2px; }
    .extra-small { font-size: 11px; letter-spacing: 0.5px; }
    .italic { font-style: italic; }
</style>
@endsection
