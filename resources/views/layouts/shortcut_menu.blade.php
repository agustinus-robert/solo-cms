<div class="dropdown d-none d-lg-inline-block ms-1">
    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-customize"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <div class="px-lg-2">
            <div class="row g-0">
                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('portal::dashboard.index') }}">
                        <i class="bx bxs-dashboard" style='font-size:30px;'></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('cms::dashboard') }}">
                        <i class="bx bxs-book-content" style='font-size:30px;'></i>
                        <span>CMS</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('account::account.dashboard') }}">
                        <i class="bx bxs-user-circle" style='font-size:30px;'></i>
                        <span>Akun Saya</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
