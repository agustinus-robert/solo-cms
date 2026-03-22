<div class="container-fluid px-5 d-none border-bottom d-lg-block">
    <div class="row gx-0 align-items-center">
        <div class="col-lg-4 text-center text-lg-start mb-lg-0">
            <div class="d-inline-flex align-items-center" style="height: 45px;">
                <small class="text-muted me-3">
                    <i class="fa fa-map-marker-alt me-2 text-primary"></i>Jl. Solo Surakarta
                </small>

                <small class="text-muted me-3">
                    <i class="fa fa-calendar-alt me-2 text-primary"></i>Senin - Sabtu
                </small>

                <small class="text-muted">
                    <i class="fa fa-clock me-2 text-primary"></i>08:00 - 17:00
                </small>
            </div>
        </div>
        <div class="col-lg-4 text-center d-flex align-items-center justify-content-center">
            <small class="text-dark">Hubungi Kami:</small>
            <a href="#" class="text-muted">(+012) 1234 567890</a>
        </div>

        <div class="col-lg-4 text-center text-lg-end">
            <div class="d-inline-flex align-items-center" style="height: 45px;">
                <a href="{{ url('/help') }}" class="text-muted me-3">
                    <small><i class="fa fa-question-circle me-1"></i> Bantuan</small>
                </a>

                <a href="https://wa.me/628123456789" class="text-muted me-3">
                    <small><i class="fa fa-phone-alt me-1"></i> Call Center</small>
                </a>

                <span class="text-muted me-3">|</span>

                @guest
                    <a href="{{ url('/login') }}" class="text-muted me-4"><small>Login</small></a>
                @endguest

                @auth
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted ms-2" data-bs-toggle="dropdown">
                            <small><i class="fa fa-user me-2"></i> {{ Auth::user()->name }}</small>
                        </a>
                        <div class="dropdown-menu rounded shadow-sm border-0 dropdown-menu-end">
                            <a href="{{ route('web::area.customer.index') }}" class="dropdown-item"><i class="fa fa-id-card me-2"></i> Profile</a>
                            <a href="{{ route('web::area.wishlist.index') }}" class="dropdown-item"><i class="fa fa-heart me-2"></i> Wishlist</a>
                            <a href="{{ route('web::web.cart.detail') }}" class="dropdown-item"><i class="fa fa-shopping-cart me-2"></i> My Cart</a>
                            <hr class="dropdown-divider">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa fa-sign-out-alt me-2"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>


    <div class="container-fluid px-5 py-4 d-none d-lg-block">
        <div class="row gx-0 align-items-center text-center">
            <div class="col-md-4 col-lg-3 text-center text-lg-start">
                <div class="d-inline-flex align-items-center">
                    <a href="" class="navbar-brand p-0">
                        <h1 class="display-5 text-primary m-0"><i
                                class="fas fa-shopping-bag text-secondary me-2"></i>SoloCT</h1>
                        <!-- <img src="img/logo.png" alt="Logo"> -->
                    </a>
                </div>
            </div>
            <div class="col-md-4 col-lg-6 text-center">
                <div class="position-relative ps-4">
                    <div class="d-flex border rounded-pill">
                        <input class="form-control border-0 rounded-pill w-100 py-3" type="text"
                            data-bs-target="#dropdownToggle123" placeholder="Search Looking For?">
                        <select class="form-select text-dark border-0 border-start rounded-0 p-3" style="width: 200px;">
                            <option value="All Category">All Category</option>
                            <option value="Pest Control-2">Category 1</option>
                            <option value="Pest Control-3">Category 2</option>
                            <option value="Pest Control-4">Category 3</option>
                            <option value="Pest Control-5">Category 4</option>
                        </select>
                        <button type="button" class="btn btn-primary rounded-pill py-3 px-5" style="border: 0;"><i
                                class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 text-center text-lg-end">
                <div class="d-inline-flex align-items-center">
                    @auth
                        @php
                            $isLiveEditorActive = request()->query('live_editor') === 'true';
                            $toggleUrl = Request::fullUrlWithQuery(['live_editor' => $isLiveEditorActive ? null : 'true']);

                            $canEditMode = $canEdit ?? false;
                        @endphp

                        <a href="{{ $toggleUrl }}"
                        class="{{ $isLiveEditorActive ? 'text-danger' : 'text-primary' }} d-flex align-items-center justify-content-center me-3"
                        title="{{ $isLiveEditorActive ? 'Matikan Live Editor' : 'Aktifkan Live Editor' }}">
                            <span class="rounded-circle btn-md-square border {{ $isLiveEditorActive ? 'border-danger' : 'border-primary' }}">
                                <i class="fas {{ $isLiveEditorActive ? 'fa-times' : 'fa-edit' }}"></i>
                            </span>
                        </a>
                    @endauth

                    @include('web::components.chart-version.electro.whistlist-corner')
                    @include('web::components.chart-version.electro.chart-corner')
                    {{-- <a href="#" class="text-muted d-flex align-items-center justify-content-center">
                        <span class="rounded-circle btn-md-square border"><i class="fas fa-shopping-cart"></i></span>
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
