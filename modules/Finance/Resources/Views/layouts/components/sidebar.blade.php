<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">

        <li class="menu-title" key="t-menu">Utama</li>
        <li>
            <a href="{{ route('finance::dashboard') }}" class="waves-effect {{ Route::is('finance::dashboard') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span key="t-dashboards">Dasbor</span>
            </a>
        </li>

        <li class="menu-title" key="t-layanan">Layanan Karyawan</li>
        <li>
            <a href="{{ route('finance::benefit.insurances.registrations.index') }}" class="waves-effect {{ Route::is('finance::benefit.insurances.*') ? 'active' : '' }}">
                <i class="bx bx-check-shield"></i>
                <span key="t-asuransi">Asuransi</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::service.overtime.manage.index') }}" class="waves-effect {{ Route::is('finance::service.overtime.*') ? 'active' : '' }}">
                <i class="bx bx-time-five"></i>
                <span key="t-lembur">Lembur</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::service.outwork.manage.index') }}" class="waves-effect {{ Route::is('finance::service.outwork.*') ? 'active' : '' }}">
                <i class="bx bx-briefcase-alt-2"></i>
                <span key="t-insentif">Insentif Kegiatan</span>
            </a>
        </li>

        <li class="menu-title" key="t-rekap">Rekapitulasi</li>
        <li>
            <a href="{{ route('finance::service.deduction.manage.index') }}" class="waves-effect {{ Route::is('finance::service.deduction.*') ? 'active' : '' }}">
                <i class="bx bx-cut"></i>
                <span key="t-potongan">Potongan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::summary.outworks.index') }}" class="waves-effect {{ Route::is('finance::summary.outworks.*') ? 'active' : '' }}">
                <i class="bx bx-list-check"></i>
                <span key="t-rekap-insentif">Insentif Kegiatan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::summary.overtimes.index') }}" class="waves-effect {{ Route::is('finance::summary.overtimes.*') ? 'active' : '' }}">
                <i class="bx bx-timer"></i>
                <span key="t-rekap-lembur">Lembur</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::summary.feastday.index') }}" class="waves-effect {{ Route::is('finance::summary.feastday.*') ? 'active' : '' }}">
                <i class="bx bx-calendar-event"></i>
                <span key="t-thr">TJ Hari Raya</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::summary.postyear.index') }}" class="waves-effect {{ Route::is('finance::summary.postyear.*') ? 'active' : '' }}">
                <i class="bx bx-money"></i>
                <span key="t-gaji13">Gaji ke-13</span>
            </a>
        </li>

        <li class="menu-title" key="t-payroll">Penggajian</li>
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-wallet"></i>
                <span key="t-penggajian">Penggajian</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('finance::payroll.templates.index') }}">Template Gaji</a></li>
                <li><a href="{{ route('finance::payroll.calculations.index') }}">Penghitungan</a></li>
                <li><a href="{{ route('finance::tax.ter-taxs.index') }}">PPh 21 TER</a></li>
                <li><a href="{{ route('finance::tax.income-taxs.index') }}">Pasal 17</a></li>
                <li><a href="{{ route('finance::payroll.validations.index') }}">Penerbitan</a></li>
            </ul>
        </li>

        <li class="menu-title" key="t-report">Pelaporan</li>
        <li>
            <a href="{{ route('finance::report.salaries.index') }}" class="waves-effect">
                <i class="bx bx-file"></i>
                <span>Penggajian</span>
            </a>
        </li>
        <li>
            <a href="{{ route('finance::report.overtimes.index') }}" class="waves-effect">
                <i class="bx bx-time"></i>
                <span>Lembur</span>
            </a>
        </li>

        <li class="menu-title" key="t-tax">Pajak</li>
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="bx bx-coin-stack"></i>
                <span key="t-perpajakan">Perpajakan</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="{{ route('finance::tax.incomes.index') }}">Bukti Potong PPh 21</a></li>
                <li><a href="{{ route('finance::tax.employeetaxs.index') }}">Informasi Wajib Pajak</a></li>
                <li><a href="{{ route('finance::tax.ptkps.index') }}">Referensi PTKP</a></li>
                <li><a href="{{ route('finance::tax.templates.index') }}">Template PPh 21</a></li>
            </ul>
        </li>
    </ul>
</div>
