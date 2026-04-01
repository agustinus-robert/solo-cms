<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">

        <li class="menu-title" key="t-menu">Utama</li>
        <li>
            <a href="{{ route('core::dashboard') }}" class="waves-effect {{ Route::is('core::dashboard') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span key="t-dashboards">Dasbor</span>
            </a>
        </li>

        <li class="menu-title" key="t-apps">Perusahaan</li>

        <li>
            <a href="{{ route('core::company.departments.index') }}" class="waves-effect {{ Route::is('core::company.departments.*') ? 'active' : '' }}">
                <i class="bx bx-git-repo-forked"></i>
                <span key="t-divisi">Divisi</span>
            </a>
        </li>

        <li>
            <a href="{{ route('core::company.position-type.index') }}" class="waves-effect {{ Route::is('core::company.position-type.*') ? 'active' : '' }}">
                <i class="bx bx-user-plus"></i>
                <span key="t-divisi">Tipe Posisi</span>
            </a>
        </li>

        <li>
            <a href="{{ route('core::company.positions.index') }}" class="waves-effect {{ Route::is('core::company.positions.*') ? 'active' : '' }}">
                <i class="bx bx-tag"></i>
                <span key="t-jabatan">Jabatan</span>
            </a>
        </li>

        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-user-voice"></i>
                <span key="t-layanan">Layanan Karyawan</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('core::company.services.leave-categories.index') }}" key="t-izin">Kategori Izin</a></li>
                <li><a href="{{ route('core::company.services.vacation-categories.index') }}" key="t-cuti">Kategori Cuti</a></li>
                <li><a href="{{ route('core::company.services.outwork-categories.index') }}" key="t-insentif">Kategori Insentif</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-lock-alt"></i>
                <span key="t-asuransi">Asuransi</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('core::company.insurances.manages.index') }}" key="t-kelola">Kelola Asuransi</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-money"></i>
                <span key="t-gaji">Penggajian</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('core::company.salaries.slips.index') }}">Slip Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.categories.index') }}">Kategori Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.components.index') }}">Komponen Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.templates.index') }}">Template Slip Gaji</a></li>
                <li><a href="{{ route('core::company.salaries.configs.index') }}">Pengaturan Slip Gaji</a></li>
            </ul>
        </li>

        <li class="menu-title" key="t-system">Sistem</li>

        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-cog"></i>
                <span key="t-settings">Pengaturan</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('core::company.moments.index') }}">Hari Libur</a></li>
            </ul>
        </li>
    </ul>
</div>
