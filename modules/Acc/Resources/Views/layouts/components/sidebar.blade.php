<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('acc::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::dashboard') ? 'active' : '' }}" href="{{ route('acc::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Transaction</li>

    <li class="{{ Request::routeIs('acc::ledger.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::ledger.index') ? 'active' : '' }}" href="{{ route('acc::ledger.index') }}">
            <i class="nav-main-link-icon mdi mdi-notebook"></i>
            <span class="nav-main-link-name">Ledger</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Report</li>

    <li class="{{ Request::routeIs('acc::trial-balance.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::trial-balance') ? 'active' : '' }}" href="{{ route('acc::trial-balance') }}">
            <i class="nav-main-link-icon mdi mdi-chart-gantt"></i>
            <span class="nav-main-link-name">Nerace Saldo</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('acc::profit-loss.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::profit-loss') ? 'active' : '' }}" href="{{ route('acc::profit-loss') }}">
            <i class="nav-main-link-icon mdi mdi-chart-areaspline-variant"></i>
            <span class="nav-main-link-name">Untung Rugi</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('acc::balance-sheet.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::balance-sheet') ? 'active' : '' }}" href="{{ route('acc::balance-sheet') }}">
            <i class="nav-main-link-icon mdi mdi-chart-bar"></i>
            <span class="nav-main-link-name">Neraca</span>
        </a>
    </li>

    <li class="menu-title" key="t-layanan">Setting</li>

    <li class="{{ Request::routeIs('acc::coa.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::coa.index') ? 'active' : '' }}" href="{{ route('acc::coa.index') }}">
            <i class="nav-main-link-icon mdi mdi-ab-testing"></i>
            <span class="nav-main-link-name">COA</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('acc::period.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::period.index') ? 'active' : '' }}" href="{{ route('acc::period.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-clock"></i>
            <span class="nav-main-link-name">Periode</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('acc::beginning-balance.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('acc::beginning-balance.index') ? 'active' : '' }}" href="{{ route('acc::beginning-balance.index') }}">
            <i class="nav-main-link-icon mdi mdi-scale-balance"></i>
            <span class="nav-main-link-name">Biaya Awal</span>
        </a>
    </li>
</ul>
