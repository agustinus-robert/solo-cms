<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('acc::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::dashboard') ? 'active' : '' }}" href="{{ route('acc::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

     <li class="menu-title" key="t-layanan">Master</li>

    <li class="{{ Request::routeIs('acc::coa.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::coa.index') ? 'active' : '' }}" href="{{ route('acc::coa.index') }}">
            <i class="nav-main-link-icon mdi mdi-ab-testing"></i>
            <span class="nav-main-link-name">COA</span>
        </a>
    </li>
</ul>
