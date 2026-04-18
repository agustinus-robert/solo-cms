<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('hotel::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::dashboard') ? 'active' : '' }}" href="{{ route('hotel::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Manajemen Ruangan</li>

    <li class="{{ Request::routeIs('hotel::room.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hotel::room.index') ? 'active' : '' }}" href="{{ route('hotel::room.index') }}">
            <i class="nav-main-link-icon mdi mdi-bed-king"></i>
            <span class="nav-main-link-name">Ruangan</span>
        </a>
    </li>
</ul>
