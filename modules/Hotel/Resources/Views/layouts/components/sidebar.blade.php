<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('hotel::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::dashboard') ? 'active' : '' }}" href="{{ route('hotel::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Manajemen Ruangan</li>

    <li class="{{ Request::routeIs('hotel::room-types.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::room-types.index') ? 'active' : '' }}" href="{{ route('hotel::room-types.index') }}">
            <i class="nav-main-link-icon mdi mdi-sofa-single-outline"></i>
            <span class="nav-main-link-name">Tipe Ruangan</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('hotel::room.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::room.index') ? 'active' : '' }}" href="{{ route('hotel::room.index') }}">
            <i class="nav-main-link-icon mdi mdi-bed-king"></i>
            <span class="nav-main-link-name">Ruangan</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Transaksi</li>

    <li class="{{ Request::routeIs('hotel::guest.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::guest.index') ? 'active' : '' }}" href="{{ route('hotel::guest.index') }}">
            <i class="nav-main-link-icon mdi mdi-account-group-outline"></i>
            <span class="nav-main-link-name">Guest</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('hotel::booking.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::booking.index') ? 'active' : '' }}" href="{{ route('hotel::booking.index') }}">
            <i class="nav-main-link-icon mdi mdi-book-edit-outline"></i>
            <span class="nav-main-link-name">Booking</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Master</li>

    <li class="{{ Request::routeIs('hotel::amenity.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::amenity.index') ? 'active' : '' }}" href="{{ route('hotel::amenity.index') }}">
            <i class="nav-main-link-icon mdi mdi-offer"></i>
            <span class="nav-main-link-name">Fasilitas</span>
        </a>
    </li>
</ul>
