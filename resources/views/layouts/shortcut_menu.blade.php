<div class="d-none d-lg-flex align-items-center ms-2">
    <a href="{{ url('/') }}" class="btn btn-primary btn-sm rounded-pill d-flex align-items-center px-3" style="height: 34px; border-width: 1.5px;" title="Lihat Website">
        <i class="bx bx-globe font-size-16 me-2"></i>
        <span class="d-none d-xl-inline-block fw-medium">Ke Website Utama</span>
    </a>

    <div class="ms-3 me-2 border-start" style="height: 24px; border-color: rgba(0,0,0,0.1) !important; opacity: 0.5;"></div>
</div>

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
                    <a class="dropdown-icon-item" href="{{ route('portal::outlet.manage-outlet.index') }}">
                        <i class="bx bx-store" style='font-size:30px;'></i>
                        <span>Outlet</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('core::dashboard') }}">
                        <i class="bx bxs-cog" style='font-size:30px;'></i>
                        <span>Referensi</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('hrms::dashboard') }}">
                        <i class="bx bx-briefcase" style='font-size:30px;'></i>
                        <span>Kepegawaian</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('finance::dashboard') }}">
                        <i class="bx bx-money" style='font-size:30px;'></i>
                        <span>Keuangan</span>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="dropdown-icon-item" href="{{ route('account::account.dashboard') }}">
                        <i class="bx bxs-user-circle" style='font-size:30px;'></i>
                        <span>Setting & Akun</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
