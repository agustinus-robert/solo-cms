<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('tour::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::dashboard') ? 'active' : '' }}" href="{{ route('tour::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Transaksi</li>

    <li class="{{ Request::routeIs('tour::booking.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::booking.index') ? 'active' : '' }}" href="{{ route('tour::booking.index') }}">
            <i class="nav-main-link-icon mdi mdi-account-group-outline"></i>
            <span class="nav-main-link-name">Booking</span>
        </a>
    </li>

    <li class="{{ (Request::routeIs('tour::package.index') || Request::routeIs('tour::package.edit') || Request::routeIs('tour::package.times')) ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ (Request::routeIs('tour::package.index') || Request::routeIs('tour::package.edit') || Request::routeIs('tour::package.times')) ? 'active' : '' }}" href="{{ route('tour::package.index') }}">
            <i class="nav-main-link-icon mdi mdi-package"></i>
            <span class="nav-main-link-name">Package</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('tour::availability.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::availability.index') ? 'active' : '' }}" href="{{ route('tour::availability.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-check"></i>
            <span class="nav-main-link-name">Availability</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Master</li>

    <li class="{{ Request::routeIs('tour::label.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::label.index') ? 'active' : '' }}" href="{{ route('tour::label.index') }}">
            <i class="nav-main-link-icon mdi mdi-label"></i>
            <span class="nav-main-link-name">Label</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('tour::location.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::location.index') ? 'active' : '' }}" href="{{ route('tour::location.index') }}">
            <i class="nav-main-link-icon mdi mdi-map-marker-radius"></i>
            <span class="nav-main-link-name">Location</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Laporan</li>

    <li class="{{ Request::routeIs('tour::package.report') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('tour::package.report') ? 'active' : '' }}" href="{{ route('tour::package.report') }}">
            <i class="nav-main-link-icon mdi mdi-calendar"></i>
            <span class="nav-main-link-name">Jadwal Paket</span>
        </a>
    </li>
</ul>
