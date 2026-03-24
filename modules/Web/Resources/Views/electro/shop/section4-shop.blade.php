<div class="container-fluid shop py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-3 wow" data-wow-delay="0.1s">
                <div class="product-categories mb-4">
                    <h4>Products Categories</h4>
                    <ul class="list-unstyled">
                        @foreach($categories as $cat)
                        <li>
                            <div class="categories-item">
                                <a href="{{ request()->fullUrlWithQuery(['category' => $cat->id]) }}"
                                   class="ajax-filter text-dark {{ request('category') == $cat->id ? 'fw-bold text-primary' : '' }}">
                                    <i class="fas fa-apple-alt text-secondary me-2"></i>
                                    {{ $cat->name }}
                                </a>
                                <span>({{ $cat->products_count }})</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="price mb-4">
                    <h4 class="mb-2">Price</h4>
                    <form id="shop-filter-form" action="{{ request()->url() }}" method="GET">
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                        <input type="range" class="form-range w-100" id="rangeInput" name="max_price"
                            min="0" max="10000000" step="50000"
                            value="{{ request('max_price', 0) }}"
                            oninput="document.getElementById('amount').value = new Intl.NumberFormat('id-ID').format(this.value)">
                        <output id="amount" name="amount" for="rangeInput">{{ number_format(request('max_price', 0), 0, ',', '.') }}</output>
                        <button type="submit" class="btn btn-sm btn-primary d-block mt-2 w-100">Filter Harga</button>
                    </form>
                </div>

                <div id="clear-filter-wrapper">
                    @if(request()->anyFilled(['category', 'q', 'max_price']))
                        <div class="clear-filter">
                            <a href="{{ request()->url() }}" class="btn btn-sm btn-danger w-100 ajax-filter">
                                <i class="fas fa-times me-2"></i> Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-9 wow" data-wow-delay="0.1s">
                <div class="row g-4 mb-4">
                    <div class="col-xl-7">
                        <form id="shop-search-form" action="{{ request()->url() }}" method="GET">
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif

                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" name="q" value="{{ request('q') }}" class="form-control p-3" placeholder="Keywords...">
                                <button type="submit" class="input-group-text p-3"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-5 text-end">
                        <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between align-items-center">
                            <label for="sort">Sort By:</label>
                            <select id="sort-select" class="border-0 form-select-sm bg-light me-3">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'low']) }}" {{ request('sort') == 'low' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'high']) }}" {{ request('sort') == 'high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div id="tab-5" class="tab-pane fade show p-0 active">
                        <div id="shop-container">
                            @include('web::electro.shop.partials.product-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('web::electro.shop.partials.shop-ajax')
