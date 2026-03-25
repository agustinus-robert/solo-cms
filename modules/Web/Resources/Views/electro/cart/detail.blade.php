@extends('web::electro.index')

@section('title', "Cart")

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <h2 class="fw-bold mb-4">Keranjang Belanja</h2>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-responsive bg-white shadow-sm rounded-4 p-4">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small uppercase">
                                <th scope="col">Produk</th>
                                <th scope="col">Harga</th>
                                <th scope="col" style="width: 150px;">Jumlah</th>
                                <th scope="col">Total</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $key => $item)
                            <tr id="cart-row-{{ $key }}">
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ (!empty($item['location']) && !empty($item['image_name'])) ? asset('uploads/'.$item['location'].'/'.$item['image_name']) : 'https://via.placeholder.com/80' }}"
                                             class="img-fluid rounded-3 me-3"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                        <div>
                                            <p class="mb-0 fw-bold">{{ $item['name'] }}</p>
                                            <small class="text-muted">SKU: {{ $item['code'] }}</small>
                                        </div>
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 fw-bold text-primary">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm border rounded-pill overflow-hidden">
                                        <button class="btn btn-link px-3 text-dark border-0 btn-update-qty" data-key="{{ $key }}" data-action="minus">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control border-0 text-center bg-transparent fw-bold qty-input"
                                               value="{{ $item['qty'] }}" readonly>
                                        <button class="btn btn-link px-3 text-dark border-0 btn-update-qty" data-key="{{ $key }}" data-action="plus">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 fw-bold text-dark">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</p>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm rounded-circle btn-remove-detail" data-key="{{ $key }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-shopping-cart fa-4x text-light mb-3"></i>
                                        <p class="text-muted">Keranjang Anda kosong.</p>
                                        <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Mulai Belanja</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

           <div class="col-lg-4">
                <div class="bg-white shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-dark">Ringkasan Belanja</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span>Diskon</span>
                        <span class="fw-bold">Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold text-dark">Total</span>
                        <span class="fs-5 fw-bold text-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('web::area.checkout.index') }}" class="btn btn-primary btn-lg rounded-pill fw-bold {{ count($items) == 0 ? 'disabled' : '' }}">
                            Checkout Sekarang
                        </a>
                        <a href="{{ route('web::web.shop') }}" class="btn btn-outline-secondary btn-lg rounded-pill">
                            Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-remove-detail').forEach(btn => {
            btn.addEventListener('click', async function() {
                const key = this.getAttribute('data-key');
                if(confirm('Hapus item ini?')) {
                    const url = "{{ route('web::web.cart.remove', '') }}/" + key;
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    if(response.ok) {
                        location.reload();
                    }
                }
            });
        });

        document.querySelectorAll('.btn-update-qty').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const key = this.getAttribute('data-key');
            });
        });
    </script>
@endpush
