<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">

        <li class="menu-title" key="t-menu">Utama</li>
        <li class="{{ Request::routeIs('core::dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('core::dashboard') }}" class="waves-effect {{ Request::routeIs('core::dashboard') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span key="t-dashboards">Dasbor</span>
            </a>
        </li>

        <li class="menu-title" key="t-apps">Perusahaan</li>

        <li class="{{ Request::routeIs('core::company.departments.*') ? 'mm-active' : '' }}">
            <a href="{{ route('core::company.departments.index') }}" class="waves-effect {{ Request::routeIs('core::company.departments.*') ? 'active' : '' }}">
                <i class="bx bx-git-repo-forked"></i>
                <span key="t-divisi">Divisi</span>
            </a>
        </li>

        <li class="{{ Request::routeIs('core::company.position-type.*') ? 'mm-active' : '' }}">
            <a href="{{ route('core::company.position-type.index') }}" class="waves-effect {{ Request::routeIs('core::company.position-type.*') ? 'active' : '' }}">
                <i class="bx bx-user-plus"></i>
                <span key="t-divisi">Tipe Posisi</span>
            </a>
        </li>

        <li class="{{ Request::routeIs('core::company.positions.*') ? 'mm-active' : '' }}">
            <a href="{{ route('core::company.positions.index') }}" class="waves-effect {{ Request::routeIs('core::company.positions.*') ? 'active' : '' }}">
                <i class="bx bx-tag"></i>
                <span key="t-jabatan">Jabatan</span>
            </a>
        </li>

        <li class="{{ Request::routeIs('core::company.services.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('core::company.services.*') ? 'active' : '' }}">
                <i class="bx bx-user-voice"></i>
                <span key="t-layanan">Layanan Karyawan</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('core::company.services.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('core::company.services.leave-categories.index') }}" class="{{ Request::routeIs('core::company.services.leave-categories.*') ? 'active' : '' }}" key="t-izin">Kategori Izin</a></li>
                <li><a href="{{ route('core::company.services.vacation-categories.index') }}" class="{{ Request::routeIs('core::company.services.vacation-categories.*') ? 'active' : '' }}" key="t-cuti">Kategori Cuti</a></li>
                <li><a href="{{ route('core::company.services.outwork-categories.index') }}" class="{{ Request::routeIs('core::company.services.outwork-categories.*') ? 'active' : '' }}" key="t-insentif">Kategori Insentif</a></li>
            </ul>
        </li>

        <li class="{{ Request::routeIs('core::company.insurances.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('core::company.insurances.*') ? 'active' : '' }}">
                <i class="bx bx-lock-alt"></i>
                <span key="t-asuransi">Asuransi</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('core::company.insurances.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('core::company.insurances.manages.index') }}" class="{{ Request::routeIs('core::company.insurances.manages.*') ? 'active' : '' }}" key="t-kelola">Kelola Asuransi</a></li>
            </ul>
        </li>

        <li class="{{ Request::routeIs('core::company.salaries.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('core::company.salaries.*') ? 'active' : '' }}">
                <i class="bx bx-money"></i>
                <span key="t-gaji">Penggajian</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('core::company.salaries.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('core::company.salaries.slips.index') }}" class="{{ Request::routeIs('core::company.salaries.slips.*') ? 'active' : '' }}">Slip Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.categories.index') }}" class="{{ Request::routeIs('core::company.salaries.categories.*') ? 'active' : '' }}">Kategori Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.components.index') }}" class="{{ Request::routeIs('core::company.salaries.components.*') ? 'active' : '' }}">Komponen Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.templates.index') }}" class="{{ Request::routeIs('core::company.salaries.templates.*') ? 'active' : '' }}">Template Slip Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.configs.index') }}" class="{{ Request::routeIs('core::company.salaries.configs.*') ? 'active' : '' }}">Pengaturan Slip Gaji</a></li>
            </ul>
        </li>

        <li class="menu-title" key="t-menu">Manajemen User</li>

        <li class="{{ Request::routeIs('core::manage-role.*') ? 'mm-active' : '' }}">
            <a class="waves-effect {{ Request::routeIs('core::manage-role.*') ? 'active' : '' }}" href="{{ route('core::manage-role.index') }}">
                <i class="bx bx-shield-quarter"></i>
                <span class="nav-main-link-name">Role</span>
            </a>
        </li>

        <li class="{{ Request::routeIs('core::manage-user.*') ? 'mm-active' : '' }}">
            <a class="waves-effect {{ Request::routeIs('core::manage-user.*') ? 'active' : '' }}" href="{{ route('core::manage-user.index') }}">
                <i class="bx bx-user"></i>
                <span class="nav-main-link-name">Pengaturan Pengguna</span>
            </a>
        </li>

        <li class="{{ Request::routeIs('core::manage-outlet.*') ? 'mm-active' : '' }}">
            <a class="waves-effect {{ Request::routeIs('core::manage-outlet.*') ? 'active' : '' }}" href="{{ route('core::manage-outlet.index') }}">
                <i class="bx bxs-store"></i>
                <span class="nav-main-link-name">Pengaturan Outlet</span>
            </a>
        </li>

        <li class="menu-title" key="t-system">Sistem</li>

        <li class="{{ Request::routeIs('core::company.moments.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('core::company.moments.*') ? 'active' : '' }}">
                <i class="bx bx-cog"></i>
                <span key="t-settings">Pengaturan</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('core::company.moments.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('core::company.moments.index') }}" class="{{ Request::routeIs('core::company.moments.*') ? 'active' : '' }}">Hari Libur</a></li>
            </ul>
        </li>
    </ul>
</div>
