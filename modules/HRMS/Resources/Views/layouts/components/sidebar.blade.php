<ul class="metismenu list-unstyled" id="side-menu">
    <li class="nav-main-item">
        <a class="nav-main-link {{ Route::is('hrms::dashboard') ? 'active' : '' }}" href="{{ route('hrms::dashboard') }}">
            <i class="nav-main-link-icon mdi mdi-apps"></i>
            <span class="nav-main-link-name">Dasbor</span>
        </a>
    </li>

    <li class="menu-title" key="t-karyawan">Karyawan</li>

    <li class="nav-main-item">
        <a class="nav-main-link {{ Route::is('hrms::employment.employees.*') ? 'active' : '' }} has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-account-box-multiple-outline"></i>
            <span class="nav-main-link-name">Data Karyawan</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::employment.employees.create', ['next' => route('hrms::employment.employees.index')]) }}">Tambah Karyawan</a></li>
            <li><a href="{{ route('hrms::employment.employees.index') }}">Kelola Karyawan</a></li>
        </ul>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link {{ Route::is('hrms::employment.contracts.*') ? 'active' : '' }} has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-file-account-outline"></i>
            <span class="nav-main-link-name">Perjanjian Kerja</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::employment.contracts.create', ['next' => route('hrms::employment.contracts.index')]) }}">Buat Baru</a></li>
            <li><a href="{{ route('hrms::employment.contracts.index') }}">Data Perjanjian</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-layanan">Layanan</li>

    <li class="nav-main-item">
        <a class="nav-main-link has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-calendar-alert"></i>
            <span class="nav-main-link-name">Presensi</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::service.attendance.schedules.index') }}">Jadwal Kerja</a></li>
            <li><a href="{{ route('hrms::service.attendance.manage.index') }}">Kelola Presensi</a></li>
            <li><a href="{{ route('hrms::service.attendance.scanlogs.index') }}">Daftar Scanlog</a></li>
        </ul>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-calendar-minus"></i>
            <span class="nav-main-link-name">Cuti</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::service.vacation.quotas.index') }}">Distribusi Kuota</a></li>
            <li><a href="{{ route('hrms::service.vacation.manage.index') }}">Kelola Cuti</a></li>
        </ul>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link {{ Route::is('hrms::service.leave.*') ? 'active' : '' }}" href="{{ route('hrms::service.leave.manage.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-export"></i>
            <span class="nav-main-link-name">Kelola Izin</span>
        </a>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link {{ Route::is('hrms::service.overtime.*') ? 'active' : '' }}" href="{{ route('hrms::service.overtime.manage.index') }}">
            <i class="nav-main-link-icon mdi mdi-sort-clock-descending-outline"></i>
            <span class="nav-main-link-name">Kelola Lembur</span>
        </a>
    </li>

    <li class="menu-title" key="t-benefit">Benefit</li>

    <li class="nav-main-item">
        <a class="nav-main-link has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-cash-check"></i>
            <span class="nav-main-link-name">Asuransi</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::benefit.insurances.registrations.index') }}">Registrasi</a></li>
            <li><a href="{{ route('hrms::benefit.insurances.templates.index') }}">Template BPJS</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-rekap">Rekapitulasi & Gaji</li>

    <li class="nav-main-item">
        <a class="nav-main-link" href="{{ route('hrms::summary.attendances.index') }}">
            <i class="nav-main-link-icon mdi mdi-calendar-multiple-check"></i>
            <span class="nav-main-link-name">Kehadiran</span>
        </a>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link has-arrow" href="javascript:void(0);">
            <i class="nav-main-link-icon mdi mdi-cash-multiple"></i>
            <span class="nav-main-link-name">Penggajian</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('hrms::payroll.approvals.index') }}">Persetujuan Gaji</a></li>
        </ul>
    </li>

    <li class="menu-title" key="t-laporan">Pelaporan</li>

    <li class="nav-main-item">
        <a class="nav-main-link" href="{{ route('hrms::report.employees.index') }}">
            <i class="nav-main-link-icon mdi mdi-file-account-outline"></i>
            <span class="nav-main-link-name">Lap. Karyawan</span>
        </a>
    </li>

    <li class="nav-main-item">
        <a class="nav-main-link" href="{{ route('hrms::report.salaries.index') }}">
            <i class="nav-main-link-icon mdi mdi-account-cash-outline"></i>
            <span class="nav-main-link-name">Lap. Penggajian</span>
        </a>
    </li>

    <li class="menu-title" key="t-system">Sistem & Akun</li>

    <li class="nav-main-item">
        <a class="nav-main-link" href="{{ route('account::manage-profile.index') }}">
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
