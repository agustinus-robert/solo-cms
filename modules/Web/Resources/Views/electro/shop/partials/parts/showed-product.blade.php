<div class="col-lg-7 col-xl-9 wow fadeInUp" data-wow-delay="0.1s">
    <div class="row g-4 single-product">
        <div class="col-xl-6">
            <div class="single-inner bg-light rounded">
                <img src="{{ asset('uploads/'. $product->location .'/' . $product->image_name) }}" class="img-fluid rounded w-100" alt="{{ $product->name }}">
            </div>
        </div>

        <div class="col-xl-6">
            <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
            <p class="mb-3">Category: {{ $product->category->name ?? 'Electronics' }}</p>

            <h5 class="fw-bold mb-3 text-primary" id="display-price">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </h5>

            @if($groupedData)
                <div id="variants-wrapper" class="mb-4">
                    @foreach($groupedData['labels'] as $index => $group)
                        <div class="variant-group mb-3" data-group-index="{{ $index }}" id="group-{{ $index }}">
                            <label class="fw-bold d-block mb-2 text-uppercase" style="font-size: 0.8rem;">
                                {{ $group['parent_name'] }}
                            </label>
                            <div class="d-flex flex-wrap gap-2 group-items">
                                @foreach($group['items'] as $item)
                                    <button type="button"
                                        class="btn btn-sm border btn-variant-option"
                                        data-tier-id="{{ $item['id'] }}"
                                        {{ $index > 0 ? 'disabled' : '' }}>
                                        {{ $item['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex flex-column mb-3">
                <small>SKU: <span id="display-sku">{{ $product->sku ?? 'N/A' }}</span></small>
                <small>Available: <strong class="text-primary" id="display-stock">{{ $product->stock ?? 0 }} items</strong></small>
            </div>

            <div class="d-flex align-items-center mb-4">
                <div class="input-group quantity me-3" style="width: 120px;">
                    <button class="btn btn-sm btn-minus rounded-circle bg-light border" id="btn-qty-minus-v2" type="button">
                        <i class="fa fa-minus"></i>
                    </button>

                    <input type="text" id="input-qty" class="form-control form-control-sm text-center border-0 bg-transparent" value="1" readonly inputmode="numeric">

                    <button class="btn btn-sm btn-plus rounded-circle bg-light border" id="btn-qty-plus-v2" type="button">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <button id="btn-add-to-cart"
                    class="btn btn-primary border border-secondary rounded-pill px-4 py-2 text-white"
                    {{ (!empty($groupedData['labels'])) ? 'disabled' : '' }}>
                    <i class="fa fa-shopping-bag me-2"></i> Add to cart
                </button>
            </div>
        </div>

        <div class="col-lg-12">
            <nav>
                <div class="nav nav-tabs mb-3">
                    <button class="nav-link active border-white border-bottom-0" type="button"
                        role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                        aria-controls="nav-about" aria-selected="true">Description</button>
                    <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                        id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                        aria-controls="nav-mission" aria-selected="false">Reviews</button>
                </div>
            </nav>
            <div class="tab-content mb-5">
                @include('web::electro.shop.partials.parts.tabbed.description')
                @include('web::electro.shop.partials.parts.tabbed.review')
            </div>


            @include('web::electro.shop.partials.parts.form-review')
        </div>
    </div>
</div>

@include('web::electro.shop.partials.parts.js.product-js')
@include('web::electro.shop.partials.parts.js.review-js')
