<div class="container-fluid product py-5">
    <div class="container py-5">
        @php
            $dynamicCategories = \Modules\Poz\Models\Category::has('products')->get();
        @endphp

        <div class="tab-class">
            <div class="row g-4">
                <div class="col-lg-4 text-start wow fadeInLeft" data-wow-delay="0.1s">
                    <h1>Our Products</h1>
                </div>
                <div class="col-lg-8 text-end wow fadeInRight" data-wow-delay="0.1s">
                    <ul class="nav nav-pills d-inline-flex text-center mb-5">
                        <li class="nav-item mb-4">
                            <a class="d-flex mx-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-all">
                                <span class="text-dark" style="width: 130px;">All Products</span>
                            </a>
                        </li>

                        @foreach($dynamicCategories as $cat)
                        <li class="nav-item mb-4">
                            <a class="d-flex py-2 mx-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-cat-{{ $cat->id }}">
                                <span class="text-dark" style="width: 130px;">{{ $cat->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div id="tab-all" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="product-item-inner border rounded">
                                        <div class="product-item-inner-item">
                                            <img src="{{ asset('uploads/'.$product->location.'/'.$product->image_name) }}" class="img-fluid w-100 rounded-top" alt="">
                                            <div class="product-new">New</div>
                                            <div class="product-details">
                                                <a href="{{ route('web::web.shop.show', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
                                            </div>
                                        </div>
                                        <div class="text-center rounded-bottom p-4">
                                            <a href="#" class="d-block mb-2">{{ $product->category->name }}</a>
                                            <a href="#" class="d-block h4">{{ $product->name }}</a>
                                            <del class="me-2 fs-5">{{ cleanRupiah($product->price) }}</del>
                                            <span class="text-primary fs-5">{{ cleanRupiah($product->wholesale) }}</span>
                                        </div>
                                    </div>
                                    <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                        @include('web::components.chart-version.electro.add-to-chart', ['productId' => $product->id])

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex">
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="d-flex">
                                                @include('web::components.chart-version.electro.whistlist', [
                                                    'productId' => $product->id,
                                                    'isWishlisted' => $product->is_wishlisted ?? false
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach($dynamicCategories as $cat)
                <div id="tab-cat-{{ $cat->id }}" class="tab-pane fade show p-0">
                    <div class="row g-4">
                        @php
                            $catProducts = \Modules\Poz\Models\Product::where('category_id', $cat->id)->latest()->take(8)->get();
                        @endphp
                        @foreach($catProducts as $product)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="product-item-inner border rounded">
                                        <div class="product-item-inner-item">
                                            <img src="{{ asset('uploads/'.$product->location.'/'.$product->image_name) }}" class="img-fluid w-100 rounded-top" alt="">
                                            <div class="product-new">New</div>
                                            <div class="product-details">
                                                <a href="{{ route('web::web.shop.show', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
                                            </div>
                                        </div>
                                        <div class="text-center rounded-bottom p-4">
                                            <a href="#" class="d-block mb-2">{{ $product->category->name }}</a>
                                            <a href="#" class="d-block h4">{{ $product->name }}</a>
                                            <del class="me-2 fs-5">{{ cleanRupiah($product->price) }}</del>
                                            <span class="text-primary fs-5">{{ cleanRupiah($product->wholesale) }}</span>
                                        </div>
                                    </div>
                                    <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                        @include('web::components.chart-version.electro.add-to-chart', ['productId' => $product->id])

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex">
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="d-flex">
                                                @include('web::components.chart-version.electro.whistlist', [
                                                    'productId' => $product->id,
                                                    'isWishlisted' => $product->is_wishlisted ?? false
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
