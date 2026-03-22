@extends('web::electro.index')
@section('title', "Daftar Checkout")

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <h1 class="mb-4 text-dark font-weight-bold">Checkout Detail</h1>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('web::area.checkout.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row g-5">
                <div class="col-md-12 col-lg-6 col-xl-7">

                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-dark font-weight-bold">Alamat Pengiriman</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddress">
                                <i class="fas fa-map-marker-alt me-1"></i> Pilih Alamat Lain
                            </button>
                        </div>

                        <div id="selected-address-display" class="border p-3 rounded bg-light mb-4">
                            @if($address)
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-primary me-2" id="display-label">{{ $address->label ?? 'Utama' }}</span>
                                    <strong id="display-phone">{{ $address->phone }}</strong>
                                </div>
                                <div id="display-text" class="text-muted small">{{ $address->address }}</div>
                            @else
                                <div class="text-danger small"><i class="fas fa-exclamation-circle"></i> Alamat belum dipilih. Silakan klik tombol di atas.</div>
                            @endif
                        </div>

                        <input type="hidden" id="main-address-id" name="address_id" value="{{ $address->id ?? '' }}">
                        <textarea id="main-address-input" name="address" class="d-none" required>{{ $address->address ?? '' }}</textarea>
                        <input type="hidden" id="main-phone-input" name="phone" value="{{ $address->phone ?? '' }}">

                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item">
                                    <label class="form-label my-2 text-muted small">Nama Penerima</label>
                                    <input type="text" disabled class="form-control shadow-sm bg-light" value="{{ $user->name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item">
                                    <label class="form-label my-2 text-muted small">Email Konfirmasi</label>
                                    <input type="email" disabled class="form-control shadow-sm bg-light" value="{{ $user->email ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 position-relative overflow-hidden">
                        <div id="payment-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center {{ $address ? 'd-none' : '' }}" style="background: rgba(255,255,255,0.85); z-index: 10;">
                            <div class="text-center">
                                <i class="fas fa-lock text-muted mb-2 h4"></i>
                                <p class="text-dark small fw-bold mb-0">Pilih alamat pengiriman dulu</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-dark font-weight-bold">Metode Pembayaran</h5>
                            <button type="button" id="btn-open-payment" class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPayment" {{ !$address ? 'disabled' : '' }}>
                                <i class="fas fa-credit-card me-1"></i> Pilih Bank
                            </button>
                        </div>

                        <div id="selected-payment-display" class="border p-3 rounded bg-light">
                            <div class="text-muted small italic">Belum ada bank yang dipilih.</div>
                        </div>
                        <input type="hidden" name="payment_method" id="input-payment-method" required>
                    </div>

                    <div class="form-item mt-4">
                        <label class="form-label text-dark small font-weight-bold">Catatan Tambahan</label>
                        <textarea name="note" class="form-control shadow-sm border-0" rows="2" placeholder="Contoh: Titip satpam..."></textarea>
                    </div>
                </div>

                <div class="col-md-12 col-lg-6 col-xl-5">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="mb-4 text-dark font-weight-bold border-bottom pb-2">Ringkasan Pesanan</h5>

                        <input type="hidden" name="outlet_id" value="1">

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td width="60">
                                            <img src="{{ asset('uploads/'.$item['product_model']->location.'/'.$item['product_model']->image_name) }}"
                                                 class="img-fluid rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td class="py-2">
                                            <div class="text-dark small font-weight-bold">{{ $item['name'] }}</div>
                                            <div class="text-muted extra-small">x{{ $item['qty'] }}</div>
                                        </td>
                                        <td class="py-2 text-end text-dark small">
                                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="2" class="pt-3 text-muted small">Subtotal</td>
                                        <td class="pt-3 text-end text-dark small fw-bold">Rp {{ number_format($totals['sub_total'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="h5">
                                        <td colspan="2" class="pt-3 font-weight-bold text-dark">Total</td>
                                        <td class="pt-3 text-end font-weight-bold text-primary">Rp {{ number_format($totals['grand_total'], 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <button type="submit" id="btn-submit" class="btn btn-primary btn-lg py-3 px-4 text-uppercase w-100 font-weight-bold text-white shadow mt-3 {{ !$address ? 'disabled' : '' }}">
                            Konfirmasi Pemesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAddress" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title font-weight-bold">Daftar Alamat Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 400px; overflow-y: auto;">
                @forelse($addresses as $addr)
                    <div class="card mb-3 address-card border {{ $addr->is_main ? 'border-primary shadow-sm' : '' }}"
                         onclick="selectAddress('{{ $addr->id }}', '{{ $addr->label }}', '{{ $addr->address }}', '{{ $addr->phone }}')"
                         style="cursor: pointer;">
                        <div class="card-body p-3">
                            <span class="badge {{ $addr->is_main ? 'bg-primary' : 'bg-secondary' }} mb-2">{{ $addr->label }}</span>
                            <p class="small text-dark mb-1 fw-bold">{{ $addr->phone }}</p>
                            <p class="small text-muted mb-0">{{ $addr->address }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada alamat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPayment" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title font-weight-bold">Pilih Bank Transfer (VA)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @foreach(['bca' => 'BCA Virtual Account', 'bni' => 'BNI Virtual Account', 'bri' => 'BRI Virtual Account', 'mandiri' => 'Mandiri Bill Payment'] as $key => $name)
                <div class="card mb-2 payment-option-card border" onclick="selectPayment('{{ $key }}', '{{ $name }}')" style="cursor: pointer;">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-dark fw-bold text-uppercase">{{ $key }}</h6>
                            <small class="text-muted">{{ $name }}</small>
                        </div>
                        <i class="fas fa-chevron-right text-light"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function selectAddress(id, label, text, phone) {
        document.getElementById('selected-address-display').innerHTML = `
            <div class="d-flex align-items-center mb-1">
                <span class="badge bg-primary me-2">${label}</span>
                <strong>${phone}</strong>
            </div>
            <div class="text-muted small">${text}</div>
        `;
        document.getElementById('main-address-id').value = id;
        document.getElementById('main-address-input').value = text;
        document.getElementById('main-phone-input').value = phone;
        document.getElementById('payment-overlay').classList.add('d-none');
        document.getElementById('btn-open-payment').disabled = false;

        var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddress'));
        if (modal) modal.hide();
    }

    function selectPayment(key, name) {
        document.getElementById('selected-payment-display').innerHTML = `
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <strong class="text-dark text-uppercase">${key}</strong><br>
                    <small class="text-muted">${name}</small>
                </div>
            </div>
        `;

        document.getElementById('input-payment-method').value = key;
        document.getElementById('btn-submit').classList.remove('disabled');
        document.getElementById('btn-submit').disabled = false;

        var modal = bootstrap.Modal.getInstance(document.getElementById('modalPayment'));
        if (modal) modal.hide();
    }
</script>

<style>
    .address-card:hover, .payment-option-card:hover { border-color: #0d6efd !important; background-color: #f0f7ff; }
    .extra-small { font-size: 10px; }
    .disabled { pointer-events: none; opacity: 0.6; }
</style>
@endsection
