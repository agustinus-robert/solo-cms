@extends('web::electro.index')
@section('title', "Daftar Checkout")

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <h1 class="mb-4 text-dark">Checkout Detail</h1>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('web::area.checkout.store') }}" method="POST">
            @csrf
            <div class="row g-5">
                <div class="col-md-12 col-lg-6 col-xl-7">
                    <div class="card border-0 shadow-sm p-4">

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
                                <div class="text-danger small"><i class="fas fa-exclamation-circle"></i> Alamat belum diatur. Silakan tambah alamat di profil.</div>
                            @endif
                        </div>

                        <input type="hidden" id="main-address-id" name="address_id" value="{{ $address->id ?? '' }}">
                        <textarea id="main-address-input" name="address" class="d-none" required>{{ $address->address ?? '' }}</textarea>
                        <input type="hidden" id="main-phone-input" name="phone" value="{{ $address->phone ?? '' }}">

                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">Nama Lengkap<sup>*</sup></label>
                                    <input type="text" name="name" disabled class="form-control shadow-sm" value="{{ $user->name ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">Email<sup>*</sup></label>
                                    <input type="email" name="email" disabled class="form-control shadow-sm" value="{{ $user->email ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3">Outlet Pengiriman<sup>*</sup></label>
                            <select name="outlet_id" class="form-select shadow-sm" required>
                                <option value="1" selected>Outlet Utama (Surakarta)</option>
                            </select>
                        </div>

                        <div class="form-item">
                            <label class="form-label my-3">Catatan Tambahan</label>
                            <textarea name="note" class="form-control shadow-sm" rows="2" placeholder="Contoh: Titip satpam atau warna cadangan..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-lg-6 col-xl-5">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h5 class="mb-4 text-dark font-weight-bold">Ringkasan Pesanan</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr class="border-bottom">
                                        <th scope="col" class="small text-muted">Produk</th>
                                        <th scope="col" class="small text-muted">Info</th>
                                        <th scope="col" class="small text-muted text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center mt-2">
                                                <img src="{{ asset('uploads/'.$item['product_model']->location.'/'.$item['product_model']->image_name) ?? asset('default-product.png') }}"
                                                     class="img-fluid rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" alt="">
                                            </div>
                                        </th>
                                        <td class="py-3">
                                            <div class="text-dark font-weight-bold small">{{ $item['name'] }}</div>
                                            <div class="text-muted extra-small">x{{ $item['qty'] }} @ Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-3 text-end text-dark small">
                                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top mt-2">
                                    <tr>
                                        <td colspan="2" class="pt-3 text-muted">Subtotal</td>
                                        <td class="pt-3 text-end text-dark font-weight-bold">Rp {{ number_format($totals['sub_total'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-muted">PPN (11%)</td>
                                        <td class="text-end text-dark font-weight-bold">Rp {{ number_format($totals['ppn'], 0, ',', '.') }}</td>
                                    </tr>
                                    @if($totals['discount'] > 0)
                                    <tr>
                                        <td colspan="2" class="text-danger">Diskon</td>
                                        <td class="text-end text-danger font-weight-bold">- Rp {{ number_format($totals['discount'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="border-top mt-2">
                                        <td colspan="2" class="pt-3 font-weight-bold text-dark h5">TOTAL</td>
                                        <td class="pt-3 text-end font-weight-bold text-primary h5">Rp {{ number_format($totals['grand_total'], 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg py-3 px-4 text-uppercase w-100 font-weight-bold text-white shadow mt-3">
                            Konfirmasi Pemesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAddress" tabindex="-1" aria-labelledby="modalAddressLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark font-weight-bold" id="modalAddressLabel">Daftar Alamat Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                @forelse($addresses as $addr)
                    <div class="card mb-2 address-card cursor-pointer border {{ $addr->is_main ? 'border-primary shadow-sm' : '' }}"
                         onclick="selectAddress('{{ $addr->id }}', '{{ $addr->label }}', '{{ $addr->address }}', '{{ $addr->phone }}')"
                         style="cursor: pointer; transition: 0.3s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">{{ $addr->label }}</strong>
                                @if($addr->is_main) <span class="badge bg-primary">Utama</span> @endif
                            </div>
                            <p class="small mb-1 text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $addr->address }}</p>
                            <p class="small mb-0 text-dark"><i class="fas fa-phone me-1"></i> {{ $addr->phone }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted">Belum ada alamat tersimpan.</p>
                        <a href="{{ route('web::area.address.index') }}" class="btn btn-sm btn-primary">Tambah Alamat Baru</a>
                    </div>
                @endforelse
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function selectAddress(id, label, text, phone) {
        document.getElementById('display-label').innerText = label;
        document.getElementById('display-text').innerText = text;
        document.getElementById('display-phone').innerText = phone;

        document.getElementById('main-address-id').value = id;
        document.getElementById('main-address-input').value = text;
        document.getElementById('main-phone-input').value = phone;

        var modalEl = document.getElementById('modalAddress');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        document.querySelectorAll('.address-card').forEach(el => el.classList.remove('border-primary', 'shadow-sm'));
        event.currentTarget.classList.add('border-primary', 'shadow-sm');
    }
</script>

<style>
    .address-card:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
    }
    .extra-small {
        font-size: 0.75rem;
    }
</style>
@endsection
