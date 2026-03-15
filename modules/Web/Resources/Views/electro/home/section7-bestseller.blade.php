<div class="container-fluid products pb-5">
    <div class="container products-mini py-5">
        <div class="mx-auto text-center mb-5" style="max-width: 700px;">
            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                data-wow-delay="0.1s">Bestseller Products</h4>
            <p class="mb-0 wow fadeInUp" data-wow-delay="0.2s">
                Koleksi produk unggulan kami yang paling banyak dipilih oleh pelanggan karena kualitas dan performanya.
            </p>
        </div>
        <div class="row g-4">
            @foreach($products as $index => $product)
                @php
                    // Logika delay: 0.1s, 0.3s, 0.5s, lalu ulang lagi ke 0.1s
                    $delay = 0.1 + (($index % 3) * 0.2);
                @endphp
                <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="{{ $delay }}s">
                    <div class="products-mini-item border">
                        <div class="row g-0">
                            <div class="col-5">
                                <div class="products-mini-img border-end h-100">
                                    <img src="{{ asset('uploads/'.$product->location.'/'.$product->image_name) }}"
                                         class="img-fluid w-100 h-100"
                                         style="object-fit: cover;"
                                         alt="{{ $product->name }}">
                                    <div class="products-mini-icon rounded-circle bg-primary">
                                        <a href="#"><i class="fa fa-eye fa-1x text-white"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="products-mini-content p-3">
                                    <a href="#" class="d-block mb-2 text-muted small">{{ $product->category->name ?? 'General' }}</a>
                                    <a href="#" class="d-block h4">{{ $product->name }}</a>

                                    @if($product->price > $product->wholesale)
                                        <del class="me-2 fs-5 text-muted">{{ cleanRupiah($product->price) }}</del>
                                    @endif
                                    <span class="text-primary fs-5 fw-bold">{{ cleanRupiah($product->wholesale) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="products-mini-add border p-3">
                            <a href="#" class="btn btn-primary border-secondary rounded-pill py-2 px-4">
                                <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                            </a>
                            <div class="d-flex">
                                <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3">
                                    <span class="rounded-circle btn-sm-square border"><i class="fas fa-random"></i></span>
                                </a>
                                <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0">
                                    <span class="rounded-circle btn-sm-square border"><i class="fas fa-heart"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
