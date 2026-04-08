<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('poz::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('poz::dashboard') ? 'active' : '' }}" href="{{ route('poz::dashboard', request()->query()) }}">
            <i class="nav-main-link-icon bx bxs-dashboard"></i>
            <span class="nav-main-link-name">Dashboard</span>
        </a>
    </li>

    <li class="menu-title" key="t-menu">Referensi</li>
    <li class="{{ Request::routeIs('poz::master.*') ? 'mm-active' : '' }}">
        <a class="has-arrow waves-effect" href="javascript:void(0)">
            <i class="nav-main-link-icon bx bx-box"></i>
            <span class="nav-main-link-name">Master Data</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('poz::master.*') ? 'mm-show' : '' }}">
            <li class="{{ Request::routeIs('poz::master.brand.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.brand.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.brand.*') ? 'active' : '' }}">Brand</a>
            </li>
            <li class="{{ Request::routeIs('poz::master.category.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.category.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.category.*') ? 'active' : '' }}">Kategori</a>
            </li>
            <li class="{{ Request::routeIs('poz::master.unit.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.unit.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.unit.*') ? 'active' : '' }}">Unit</a>
            </li>
            <li class="{{ Request::routeIs('poz::master.tax.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.tax.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.tax.*') ? 'active' : '' }}">Pajak</a>
            </li>
            <li class="{{ Request::routeIs('poz::master.supplier.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.supplier.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.supplier.*') ? 'active' : '' }}">Supplier</a>
            </li>
            <li class="{{ Request::routeIs('poz::master.tier.*') ? 'mm-active' : '' }}">
                <a href="{{ route('poz::master.tier.index', request()->query()) }}" class="{{ Request::routeIs('poz::master.tier.*') ? 'active' : '' }}">Tier</a>
            </li>
        </ul>
    </li>

    <li class="menu-title" key="t-menu">Penjadwalan</li>
    <li class="{{ Request::routeIs('poz::schedule.supplier_schedule.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('poz::schedule.supplier_schedule.*') ? 'active' : '' }}" href="{{ route('poz::schedule.supplier_schedule.index', request()->query()) }}">
            <i class="nav-main-link-icon bx bx-calendar-event"></i>
            <span class="nav-main-link-name">Jadwal Supplier</span>
        </a>
    </li>

    <li class="menu-title" key="t-menu">Transaksi</li>

    <li class="{{ Request::routeIs('poz::transaction.qutation.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('poz::transaction.qutation.*') ? 'active' : '' }}" href="{{ route('poz::transaction.qutation.index', request()->query()) }}">
            <i class="nav-main-link-icon mdi mdi-quora"></i>
            <span class="nav-main-link-name">Penawaran</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('poz::transaction.adjustment.*') ? 'mm-active' : '' }}">
        <a class="has-arrow waves-effect" href="javascript:void(0)">
            <i class="nav-main-link-icon bx bx-add-to-queue"></i>
            <span class="nav-main-link-name">Adjustment</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('poz::transaction.adjustment.*') ? 'mm-show' : '' }}">
            <li><a href="{{ route('poz::transaction.adjustment.index', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.adjustment.*') ? 'active' : '' }}">Kelola</a></li>
        </ul>
    </li>

    <li class="{{ Request::routeIs('poz::transaction.product*', 'poz::transaction.tier-variant.*', 'poz::transaction.product-promotion.*') ? 'mm-active' : '' }}">
        <a class="has-arrow waves-effect" href="javascript:void(0)">
            <i class="nav-main-link-icon bx bxs-archive"></i>
            <span class="nav-main-link-name">Product</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('poz::transaction.product*', 'poz::transaction.tier-variant.*', 'poz::transaction.product-promotion.*') ? 'mm-show' : '' }}">
            <li><a href="{{ route('poz::transaction.tier-variant.index', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.tier-variant.*') ? 'active' : '' }}">Variant Tier</a></li>
            <li><a href="{{ route('poz::transaction.product-promotion.index', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.product-promotion.*') ? 'active' : '' }}">Promo</a></li>
            <li><a href="{{ route('poz::transaction.product.index', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.product.index') ? 'active' : '' }}">Kelola</a></li>
        </ul>
    </li>

    <li class="{{ Request::routeIs('poz::transaction.sale.*') ? 'mm-active' : '' }}">
        <a class="has-arrow waves-effect" href="javascript:void(0)">
            <i class="nav-main-link-icon bx bxs-cart"></i>
            <span class="nav-main-link-name">Penjualan</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('poz::transaction.sale.*') ? 'mm-show' : '' }}">
            <li><a href="{{ route('poz::transaction.sale.index', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.sale.index') ? 'active' : '' }}">Daftar Penjualan</a></li>
            <li><a href="{{ route('poz::transaction.sale.create', request()->query()) }}" class="{{ Request::routeIs('poz::transaction.sale.create') && !request()->has('pos') ? 'active' : '' }}">Penjualan Reguler</a></li>
            <li><a href="{{ route('poz::transaction.sale.create', array_merge(request()->query(), ['pos' => 'true'])) }}" class="{{ request()->get('pos') == 'true' ? 'active' : '' }}">Penjualan POS</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-menu">Laporan</li>
    <li class="{{ Request::routeIs('poz::reporting.product_reporting.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('poz::reporting.product_reporting.*') ? 'active' : '' }}" href="{{ route('poz::reporting.product_reporting.index', request()->query()) }}">
            <i class="nav-main-link-icon bx bxs-report"></i>
            <span class="nav-main-link-name">Reporting</span>
        </a>
    </li>
    <li class="{{ Request::routeIs('poz::reporting.product_supplier_reporting.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('poz::reporting.product_supplier_reporting.*') ? 'active' : '' }}" href="{{ route('poz::reporting.product_supplier_reporting.index', request()->query()) }}">
            <i class="nav-main-link-icon bx bx-file"></i>
            <span class="nav-main-link-name">Supplier Shift</span>
        </a>
    </li>
</ul>
