<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="p-4 text-center bg-light border-bottom">
            <img src="{{ $user->profile && $user->profile->avatar ? asset('uploads/'.$user->profile->avatar) : asset('img/default-avatar.png') }}"
                 class="rounded-circle border mb-3"
                 style="width:80px; height:80px; object-fit: cover;">
            <h6 class="fw-bold mb-0 text-truncate">{{ $user->name }}</h6>
            <span class="text-muted small text-truncate d-block">{{ $user->email }}</span>
        </div>

        <div class="list-group list-group-flush">
            <li class="list-group-item bg-light small fw-bold text-muted text-uppercase border-0 py-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                Data Diri
            </li>

            <a href="{{ route('web::area.customer.index') }}"
            class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('electro.customer.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle me-3"></i> Profil Saya
            </a>

            <a href="{{ route('web::area.address.index') }}"
            class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('electro.address.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt me-3"></i> Alamat
            </a>

            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
                <i class="bi bi-heart me-3"></i> Wishlist
            </a>

            <hr class="my-1 text-muted opacity-25">

            <li class="list-group-item bg-light small fw-bold text-muted text-uppercase border-0 py-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                Penawaran Menarik
            </li>

            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
            <i class="bi bi-credit-card-2-front-fill me-3"></i> Kupon Saya
            </a>

            <a href="#" class="list-group-item list-group-item-action border-0 py-3">
                <i class="bi bi-percent me-3"></i> Promo
            </a>

            <hr class="m-0 text-muted">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="list-group-item list-group-item-action border-0 py-3 text-danger w-100 text-start">
                    <i class="bi bi-box-arrow-right me-3"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</div>
