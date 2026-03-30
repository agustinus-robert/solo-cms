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
                    <a class="nav-link" href="{{ route('finance::dashboard') }}"> <i class="mdi mdi-apps"></i> Dasbor </a>
                </li>
                <li class="divider">Layanan karyawan</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::benefit.insurances.registrations.index') }}"> <i class="mdi mdi-cash-check"></i> Asuransi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::service.overtime.manage.index') }}"> <i class="mdi mdi-sort-clock-descending-outline"></i> Lembur </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::service.outwork.manage.index') }}"> <i class="mdi mdi-comment-quote-outline"></i> Insentif kegiatan</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link {{ config('modules.finance.features.loans.state') }}" href="{{ route('finance::service.loans.index') }}"> <i class="mdi mdi-account-cash-outline"></i> Pinjaman </a>
                </li> --}}
                {{-- <li class="divider">Potongan</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-scissors-cutting"></i> Potongan</a>
                    <ul class="submenu collapse">
                        <li class="nav-item"><a class="nav-link" href="{{ route('finance::service.deduction.manage.index') }}">Daftar potongan</a></li>
                    </ul>
                </li> --}}
                <li class="divider">Rekapitulasi</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::service.deduction.manage.index') }}"> <i class="mdi mdi-scissors-cutting"></i> Potongan </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.outworks.index') }}"> <i class="mdi mdi-comment-quote-outline"></i> Insentif kegiatan </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.overtimes.index') }}"> <i class="mdi mdi-sort-clock-ascending-outline"></i> Lembur </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link disabled" href="{{ route('finance::summary.deductions.index') }}"> <i class="mdi mdi-scissors-cutting"></i> Potongan </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.teachings.index') }}"> <i class="mdi mdi-clipboard-edit-outline"></i> Pengajaran </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.coords.index') }}"> <i class="mdi mdi-clipboard-edit-outline"></i> Koordinator Siswa </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.feastday.index') }}"> <i class="mdi mdi-mosque"></i> TJ Hari Raya </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::summary.postyear.index') }}"> <i class="mdi mdi-forwardburger"></i> Gaji ke-13 </a>
                </li>
                <li class="divider">Penggajian</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-contactless-payment"></i> Penggajian</a>
                    <ul class="submenu collapse">
                        <li class="nav-item"><a class="nav-link" href="{{ route('finance::payroll.templates.index') }}">Template gaji</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('finance::payroll.calculations.index') }}">Penghitungan</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.ter-taxs.index') }}"> PPh 21 TER</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.income-taxs.index') }}"> Pasal 17</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('finance::payroll.validations.index') }}">Penerbitan</a></li>
                    </ul>
                </li>
                <li class="divider">Pelaporan</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::report.salaries.index') }}"> <i class="mdi mdi mdi-cash-check"></i> Penggajian </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('finance::report.overtimes.index') }}"> <i class="mdi mdi-sort-clock-ascending-outline"></i> Lembur </a>
                </li>
                <li class="divider">Pajak</li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="javascript:;"> <i class="mdi mdi-currency-usd-off"></i> Perpajakan</a>
                    <ul class="submenu collapse">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.incomes.index') }}"> Bukti potong PPh 21 </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.employeetaxs.index') }}"> Informasi wajib pajak </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.ptkps.index') }}"> Referensi PTKP </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance::tax.templates.index') }}"> Template PPh 21 </a>
                        </li>
                    </ul>
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
