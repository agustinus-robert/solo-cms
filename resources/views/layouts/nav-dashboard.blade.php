<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="navbar-collapse collapse" id="topnav-menu-content">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link arrow-none {{ request()->routeIs('portal::dashboard-msdm.index') ? 'active' : '' }}"
                           href="{{ route('portal::dashboard-msdm.index') }}" id="topnav-uielement" role="button">
                            <i class=" bx bxs-dashboard me-2"></i>
                            <span key="t-ui-elements"> Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link arrow-none {{ request()->routeIs('portal::dashboard.index') ? 'active' : '' }}"
                           href="{{ route('portal::dashboard.index') }}" id="topnav-dashboard" role="button">
                            <i class="bx bx-home-circle me-2"></i><span key="t-dashboards">Ringkasan Penjualan</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link arrow-none {{ request()->routeIs('portal::dashboard-supplier.index') ? 'active' : '' }}"
                           href="{{ route('portal::dashboard-supplier.index') }}" id="topnav-dashboard" role="button">
                            <i class="bx bxs-business me-2"></i><span key="t-dashboards">Ringkasan Supplier</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link arrow-none {{ request()->routeIs('portal::outlet.manage-outlet.*') ? 'active' : '' }}"
                           href="{{ route('portal::outlet.manage-outlet.index') }}" id="topnav-uielement" role="button">
                            <i class="bx bx-building-house me-2"></i>
                            <span key="t-ui-elements"> Kelola Outlet</span>
                        </a>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>
