<ul class="metismenu list-unstyled" id="side-menu">
    <li class="{{ Request::routeIs('hrms::dashboard') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::dashboard') ? 'active' : '' }}" href="{{ route('hrms::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-karyawan">Karyawan</li>

    <li class="{{ Request::routeIs('hrms::employment.employees.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::employment.employees.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-account-box-multiple-outline"></i>
            <span class="nav-main-link-name">Data Karyawan</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::employment.employees.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::employment.employees.create', ['next' => route('hrms::employment.employees.index')]) }}" class="{{ Request::routeIs('hrms::employment.employees.create') ? 'active' : '' }}">Tambah Karyawan</a></li>
            <li><a href="{{ route('hrms::employment.employees.index') }}" class="{{ Request::routeIs('hrms::employment.employees.index') ? 'active' : '' }}">Kelola Karyawan</a></li>
        </ul>
    </li>

    <li class="{{ Request::routeIs('hrms::employment.contracts.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::employment.contracts.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-file-account-outline"></i>
            <span class="nav-main-link-name">Perjanjian Kerja</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::employment.contracts.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::employment.contracts.create', ['next' => route('hrms::employment.contracts.index')]) }}" class="{{ Request::routeIs('hrms::employment.contracts.create') ? 'active' : '' }}">Buat Baru</a></li>
            <li><a href="{{ route('hrms::employment.contracts.index') }}" class="{{ Request::routeIs('hrms::employment.contracts.index') ? 'active' : '' }}">Data Perjanjian</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-layanan">Layanan</li>

    <li class="{{ Request::routeIs('hrms::service.attendance.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::service.attendance.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-calendar-alert"></i>
            <span class="nav-main-link-name">Presensi</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::service.attendance.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::service.attendance.schedules.index') }}" class="{{ Request::routeIs('hrms::service.attendance.schedules.*') ? 'active' : '' }}">Jadwal Kerja</a></li>
            <li><a href="{{ route('hrms::service.attendance.manage.index') }}" class="{{ Request::routeIs('hrms::service.attendance.manage.*') ? 'active' : '' }}">Kelola Presensi</a></li>
            <li><a href="{{ route('hrms::service.attendance.scanlogs.index') }}" class="{{ Request::routeIs('hrms::service.attendance.scanlogs.*') ? 'active' : '' }}">Daftar Scanlog</a></li>
        </ul>
    </li>

    <li class="{{ Request::routeIs('hrms::service.vacation.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::service.vacation.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-calendar-minus"></i>
            <span class="nav-main-link-name">Cuti</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::service.vacation.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::service.vacation.quotas.index') }}" class="{{ Request::routeIs('hrms::service.vacation.quotas.*') ? 'active' : '' }}">Distribusi Kuota</a></li>
            <li><a href="{{ route('hrms::service.vacation.manage.index') }}" class="{{ Request::routeIs('hrms::service.vacation.manage.*') ? 'active' : '' }}">Kelola Cuti</a></li>
        </ul>
    </li>

    <li class="{{ Request::routeIs('hrms::service.leave.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::service.leave.*') ? 'active' : '' }}" href="{{ route('hrms::service.leave.manage.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-export"></i>
            <span class="nav-main-link-name">Kelola Izin</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('hrms::service.overtime.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::service.overtime.*') ? 'active' : '' }}" href="{{ route('hrms::service.overtime.manage.index') }}">
            <i class="nav-main-link-icon mdi mdi-sort-clock-descending-outline"></i>
            <span class="nav-main-link-name">Kelola Lembur</span>
        </a>
    </li>

    <li class="menu-title" key="t-benefit">Benefit</li>

    <li class="{{ Request::routeIs('hrms::benefit.insurances.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::benefit.insurances.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-cash-check"></i>
            <span class="nav-main-link-name">Asuransi</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::benefit.insurances.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::benefit.insurances.registrations.index') }}" class="{{ Request::routeIs('hrms::benefit.insurances.registrations.*') ? 'active' : '' }}">Registrasi</a></li>
            <li><a href="{{ route('hrms::benefit.insurances.templates.index') }}" class="{{ Request::routeIs('hrms::benefit.insurances.templates.*') ? 'active' : '' }}">Template BPJS</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-rekap">Rekapitulasi & Gaji</li>

    <li class="{{ Request::routeIs('hrms::summary.attendances.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::summary.attendances.*') ? 'active' : '' }}" href="{{ route('hrms::summary.attendances.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-multiple-check"></i>
            <span class="nav-main-link-name">Kehadiran</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('hrms::payroll.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link has-arrow {{ Request::routeIs('hrms::payroll.*') ? 'active' : '' }}" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-cash-multiple"></i>
            <span class="nav-main-link-name">Penggajian</span>
        </a>
        <ul class="sub-menu mm-collapse {{ Request::routeIs('hrms::payroll.*') ? 'mm-show' : '' }}" aria-expanded="false">
            <li><a href="{{ route('hrms::payroll.approvals.index') }}" class="{{ Request::routeIs('hrms::payroll.approvals.*') ? 'active' : '' }}">Persetujuan Gaji</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-laporan">Pelaporan</li>

    <li class="{{ Request::routeIs('hrms::report.employees.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::report.employees.*') ? 'active' : '' }}" href="{{ route('hrms::report.employees.index') }}">
            <i class="nav-main-link-icon mdi mdi-file-account-outline"></i>
            <span class="nav-main-link-name">Lap. Karyawan</span>
        </a>
    </li>

    <li class="{{ Request::routeIs('hrms::report.salaries.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('hrms::report.salaries.*') ? 'active' : '' }}" href="{{ route('hrms::report.salaries.index') }}">
            <i class="nav-main-link-icon mdi mdi-account-cash-outline"></i>
            <span class="nav-main-link-name">Lap. Penggajian</span>
        </a>
    </li>

    <li class="menu-title" key="t-system">Sistem & Akun</li>

    <li class="{{ Request::routeIs('account::manage-profile.*') ? 'mm-active' : '' }}">
        <a class="nav-main-link {{ Request::routeIs('account::manage-profile.*') ? 'active' : '' }}" href="{{ route('account::manage-profile.index') }}">
            <i class="nav-main-link-icon mdi mdi-account-outline"></i>
            <span class="nav-main-link-name">Akun Saya</span>
        </a>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link text-danger" href="javascript:void(0);" onclick="signout()">
            <i class="nav-main-link-icon mdi mdi-logout text-danger"></i>
            <span class="nav-main-link-name">Keluar</span>
        </a>
    </li>
</ul>
