<div class="row g-4 product">
    @forelse($products as $product)
        <div class="col-lg-4">
            <div class="product-item rounded wow fadeInUp" data-wow-delay="0.2s">
                <div class="product-item-inner border rounded">
                    <div class="product-item-inner-item position-relative">
                        <img src="{{ asset('uploads/'.$product->location.'/'.$product->image_name) }}"
                                class="img-fluid w-100 rounded-top" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                        <div class="product-new">New</div>
                        <div class="product-details">
                            <a href="{{ route('web::web.shop.show', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
                        </div>
                    </div>
                    <div class="text-center rounded-bottom p-4">
                        <small class="text-muted d-block">{{ $product->category->name ?? 'Uncategorized' }}</small>
                        <a href="{{ route('web::web.shop.show', $product->id) }}" class="d-block h5 text-dark mt-2">{{ $product->name }}</a>
                        <del class="me-2 text-muted">{{ cleanRupiah($product->price) }}</del>
                        <span class="text-primary fw-bold">{{ cleanRupiah($product->wholesale) }}</span>
                    </div>
                </div>
                <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                    @include('web::components.chart-version.electro.add-to-chart', ['productId' => $product->id])
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex small text-primary">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="d-flex">
                            @include('web::components.chart-version.electro.whistlist',
                            [
                                'productId' => $product->id,
                                'isWishlisted' => $product->is_wishlisted ?? false
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 my-5 wow fadeIn" data-wow-delay="0.3s">
            <div class="mb-4">
                <i class="fas fa-search text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
            </div>
            <h3 class="display-6 fw-bold">Produk Tidak Ditemukan</h3>
            <p class="text-muted fs-5">Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter pencarianmu.</p>
            <div class="mt-4">
                <a href="{{ request()->url() }}" class="btn btn-primary rounded-pill px-5 py-3 ajax-filter">
                    <i class="fas fa-sync-alt me-2"></i> Reset Filter & Lihat Semua Produk
                </a>
            </div>
        </div>
    @endforelse

    @if($products->count() > 0)
        <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
            <div class="pagination d-flex justify-content-center mt-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
