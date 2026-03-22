@extends('web::electro.index')
@section('title', 'Wishlist Saya')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold"><i class="fas fa-heart text-danger me-2"></i> Wishlist Saya</h2>
        <span class="badge bg-dark rounded-pill">{{ $products->count() }} Produk</span>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-5 bg-light rounded-4">
            <i class="fas fa-heart-broken fa-4x text-muted mb-3"></i>
            <h4>Wishlist kamu masih kosong</h4>
            <p class="text-muted">Yuk, cari barang menarik dan simpan di sini!</p>
            <a href="/" class="btn btn-primary px-4 rounded-pill">Mulai Belanja</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-3 col-6" id="product-{{ $product->id }}">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <img src="{{ asset('uploads/'.$product->location.'/'.$product->image_name) }}" class="card-img-top p-3" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h6 class="card-title text-truncate">{{ $product->name }}</h6>
                            <p class="text-primary fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="toggleWishlist({{ $product->id }})">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                                <a href="/product/{{ $product->slug }}" class="btn btn-primary btn-sm rounded-pill">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleWishlist(productId) {
    if(!confirm('Hapus produk ini dari wishlist?')) return;

    fetch("{{ route('web::area.wishlist.toggle') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('product-' + productId).style.opacity = '0';
            setTimeout(() => {
                location.reload();
            }, 300);
        }
    });
}
</script>
@endpush
@endsection
