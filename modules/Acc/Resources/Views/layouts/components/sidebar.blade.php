<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('acc::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::dashboard') ? 'active' : '' }}" href="{{ route('acc::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>
</ul>
