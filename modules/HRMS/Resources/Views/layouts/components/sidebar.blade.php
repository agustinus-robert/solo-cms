<div class="sidebar bg-dark open border-end text-white" style="z-index: 9999;">
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-center border-bottom text-center" style="height: 100px;">
            <img width="80" height="70" src="{{ asset('img/icons/logo.svg') }}" alt="">
        </div>
    </div>
    <div class="sidebar-body">
        <div class="sidebar-body-menu">
            @include('x-core::Sidebar.apps')
            <ul class="nav flex-column">
                <li class="divider">Utama</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::dashboard') }}"> <i class="mdi mdi-apps"></i> Dasbor </a>
                </li>
                <li class="divider">Karyawan</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-account-box-multiple-outline"></i> Data karyawan</a>
                    <ul class="submenu collapse">
                        <li class="nav-item disactive ms-0"><a class="nav-link" href="{{ route('hrms::employment.employees.create', ['next' => route('hrms::employment.employees.index')]) }}">Tambah karyawan</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::employment.employees.index') }}">Kelola karyawan</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-file-account-outline"></i> Perjanjian kerja</a>
                    <ul class="submenu collapse">
                        <li class="nav-item disactive ms-0"><a class="nav-link" href="{{ route('hrms::employment.contracts.create', ['next' => route('hrms::employment.contracts.index')]) }}">Buat baru</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::employment.contracts.index') }}">Data perjanjian kerja</a></li>
                    </ul>
                </li>
                <li class="divider">Layanan</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-calendar-alert"></i> Presensi</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.schedules.index') }}">Jadwal kerja</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.manage.index') }}">Kelola presensi</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.scanlogs.index') }}">Daftar scanlog</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-calendar-minus"></i> Cuti</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.vacation.quotas.index') }}">Distribusi kuota</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.vacation.manage.index') }}">Kelola cuti</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-calendar-export"></i> Izin</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.leave.manage.index') }}">Kelola izin</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-sort-clock-descending-outline"></i> Lembur</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.overtime.manage.index') }}">Kelola lembur</a></li>
                    </ul>
                </li>
                <li class="divider">Benefit</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-cash-check"></i> Asuransi</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::benefit.insurances.registrations.index') }}">Registrasi</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::benefit.insurances.templates.index') }}">Template Penghitungan BPJS</a></li>
                    </ul>
                </li>
                <li class="divider">Rekapitulasi</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::summary.attendances.index') }}"> <i class="mdi mdi-calendar-multiple-check"></i> Kehadiran </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::summary.feastdays.index') }}"> <i class="mdi mdi-mosque"></i> Tj hari raya </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::summary.postyears.index') }}"> <i class="mdi mdi-forwardburger"></i> Gaji ke-13 </a>
                </li> --}}

                {{-- <li class="divider">Layanan</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-calendar-alert"></i> Presensi</a>
                    <ul class="submenu collapse">
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.schedules.index') }}">Jadwal kerja</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.manage.index') }}">Kelola presensi</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::service.attendance.scanlogs.index') }}">Daftar scanlog</a></li>
                    </ul>
                </li> --}}
                
                <li class="divider">Penggajian</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-cash-check"></i> Penggajian</a>
                    <ul class="submenu collapse">
                        {{-- <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::payroll.templates.index') }}">Template gaji</a></li>
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::payroll.calculations.index') }}">Penghitungan</a></li> --}}
                        <li class="nav-item ms-0"><a class="nav-link" href="{{ route('hrms::payroll.approvals.index') }}">Persetujuan</a></li>
                    </ul>
                </li>

                {{-- <li class="divider">Pengguna</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::system.users.index') }}"> <i class="mdi mdi-file-account-outline"></i> Kelola Pengguna </a>
                </li> --}}

                <li class="divider">Pelaporan</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::report.employees.index') }}"> <i class="mdi mdi-file-account-outline"></i> Karyawan </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::report.attendances.index') }}"> <i class="mdi mdi-file-check-outline"></i> Kehadiran </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::report.vacations.index') }}"> <i class="mdi mdi-file-import-outline"></i> Cuti </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::report.leaves.index') }}"> <i class="mdi mdi-file-clock-outline"></i> Izin </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::report.salaries.index') }}"> <i class="mdi mdi-account-cash-outline"></i> Penggajian </a>
                </li>

                <li class="divider">Support</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hrms::summary.ticket.index') }}"> <i class="mdi mdi-ticket-outline"></i> Ticket </a>
                </li>

                <li class="divider">Akun</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('account::home') }}"> <i class="mdi mdi-account-outline"></i> Akun saya </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('account::user.password', ['next' => url()->full()]) }}"> <i class="mdi mdi-lock-open-outline"></i> Ubah sandi </a>
                </li>
                <li class="nav-item">
                    <button class="btn w-100 nav-link text-danger" onclick="signout()"> <i class="mdi mdi-logout text-danger"></i> Keluar </button>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-footer">
        <div class="rounded-3 d-flex align-items-center flex-row p-3" style="background: rgba(200, 200, 200, .1);">
            <div class="rounded-circle me-3" style="width: 48px; height: 48px; background: url('{{ Auth::user()->profile_avatar_path }}') center center no-repeat; background-size: cover;"></div>
            <div class="flex-grow-1">
                <div class="fw-bold mb-0">{{ Str::limit(Auth::user()->name, 15) }}</div>
                <div class="small" style="color: rgba(150, 150, 150, .9)">{{ Str::limit(Auth::user()->email_address, 20) }}</div>
            </div>
        </div>
    </div>
</div>
