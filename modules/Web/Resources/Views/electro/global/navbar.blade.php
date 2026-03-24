@php
    $globalCategories = \Modules\Poz\Models\Category::withCount('products')->get();
@endphp

<div class="container-fluid nav-bar p-0">
    <div class="row gx-0 bg-primary px-5 align-items-center">
        <div class="col-lg-3 d-none d-lg-block">
            <nav class="navbar navbar-light position-relative" style="width: 250px;">
                <button class="navbar-toggler border-0 fs-4 w-100 px-0 text-start" type="button"
                    data-bs-toggle="collapse" data-bs-target="#allCat">
                    <h4 class="m-0 text-white"><i class="fa fa-bars me-2"></i>All Categories</h4>
                </button>
                <div class="collapse navbar-collapse rounded-bottom bg-white" id="allCat">
                    <div class="navbar-nav ms-auto py-0">
                        <ul class="list-unstyled categories-bars">
                            @foreach($globalCategories as $cat)
                            <li>
                                <div class="categories-bars-item">
                                    <a href="{{ route('web::web.shop', ['category' => $cat->id]) }}">
                                        {{ $cat->name }}
                                    </a>
                                    <span>({{ $cat->products_count }})</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col-12 col-lg-9">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary">
                <a href="{{ url('/') }}" class="navbar-brand d-block d-lg-none">
                    <h1 class="display-5 text-secondary m-0"><i
                            class="fas fa-shopping-bag text-white me-2"></i>Electro</h1>
                </a>
                <button class="navbar-toggler ms-auto bg-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars fa-1x"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="{{ url('/') }}" class="nav-item nav-link text-white {{ (Request::is('/') || Request::is('home*')) ? 'active' : '' }}">Home</a>
                        <a href="{{ route('web::web.shop') }}" class="nav-item nav-link text-white {{ Request::is('shop*') ? 'active' : '' }}">Shop</a>
                        <a href="{{ route('web::web.contact') }}" class="nav-item nav-link text-white {{ Route::is('web::web.contact') ? 'active' : '' }}">Contact</a>

                        <div class="nav-item dropdown d-block d-lg-none mb-3">
                            <a href="#" class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown">All Category</a>
                            <div class="dropdown-menu m-0">
                                <ul class="list-unstyled categories-bars">
                                    @foreach($globalCategories as $cat)
                                    <li>
                                        <div class="categories-bars-item px-3 py-2">
                                            <a href="{{ route('web::web.shop', ['category' => $cat->id]) }}">
                                                {{ $cat->name }}
                                            </a>
                                            <span class="text-muted">({{ $cat->products_count }})</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="tel:+01234567890" class="btn btn-secondary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0">
                        <i class="fa fa-mobile-alt me-2"></i> +0123 456 7890
                    </a>
                </div>
            </nav>
        </div>
    </div>
</div>
