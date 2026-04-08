<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">

        <li class="menu-title" key="t-menu">Utama</li>
        <li class="{{ Request::routeIs('finance::dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::dashboard') }}" class="waves-effect {{ Request::routeIs('finance::dashboard') ? 'active' : '' }}">
                <i class="bx bx-home-circle"></i>
                <span key="t-dashboards">Dasbor</span>
            </a>
        </li>

        <li class="menu-title" key="t-layanan">Layanan Karyawan</li>
        <li class="{{ Request::routeIs('finance::benefit.insurances.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::benefit.insurances.registrations.index') }}" class="waves-effect {{ Request::routeIs('finance::benefit.insurances.*') ? 'active' : '' }}">
                <i class="bx bx-check-shield"></i>
                <span key="t-asuransi">Asuransi</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::service.overtime.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::service.overtime.manage.index') }}" class="waves-effect {{ Request::routeIs('finance::service.overtime.*') ? 'active' : '' }}">
                <i class="bx bx-time-five"></i>
                <span key="t-lembur">Lembur</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::service.outwork.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::service.outwork.manage.index') }}" class="waves-effect {{ Request::routeIs('finance::service.outwork.*') ? 'active' : '' }}">
                <i class="bx bx-briefcase-alt-2"></i>
                <span key="t-insentif">Insentif Kegiatan</span>
            </a>
        </li>

        <li class="menu-title" key="t-rekap">Rekapitulasi</li>
        <li class="{{ Request::routeIs('finance::service.deduction.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::service.deduction.manage.index') }}" class="waves-effect {{ Request::routeIs('finance::service.deduction.*') ? 'active' : '' }}">
                <i class="bx bx-cut"></i>
                <span key="t-potongan">Potongan</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::summary.outworks.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::summary.outworks.index') }}" class="waves-effect {{ Request::routeIs('finance::summary.outworks.*') ? 'active' : '' }}">
                <i class="bx bx-list-check"></i>
                <span key="t-rekap-insentif">Insentif Kegiatan</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::summary.overtimes.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::summary.overtimes.index') }}" class="waves-effect {{ Request::routeIs('finance::summary.overtimes.*') ? 'active' : '' }}">
                <i class="bx bx-timer"></i>
                <span key="t-rekap-lembur">Lembur</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::summary.feastday.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::summary.feastday.index') }}" class="waves-effect {{ Request::routeIs('finance::summary.feastday.*') ? 'active' : '' }}">
                <i class="bx bx-calendar-event"></i>
                <span key="t-thr">TJ Hari Raya</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::summary.postyear.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::summary.postyear.index') }}" class="waves-effect {{ Request::routeIs('finance::summary.postyear.*') ? 'active' : '' }}">
                <i class="bx bx-money"></i>
                <span key="t-gaji13">Gaji ke-13</span>
            </a>
        </li>

        <li class="menu-title" key="t-payroll">Penggajian</li>
        <li class="{{ Request::routeIs('finance::payroll.*', 'finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('finance::payroll.*', 'finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'active' : '' }}">
                <i class="bx bx-wallet"></i>
                <span key="t-penggajian">Penggajian</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('finance::payroll.*', 'finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('finance::payroll.templates.index') }}" class="{{ Request::routeIs('finance::payroll.templates.*') ? 'active' : '' }}">Template Gaji</a></li>
                <li><a href="{{ route('finance::payroll.calculations.index') }}" class="{{ Request::routeIs('finance::payroll.calculations.*') ? 'active' : '' }}">Penghitungan</a></li>
                <li><a href="{{ route('finance::tax.ter-taxs.index') }}" class="{{ Request::routeIs('finance::tax.ter-taxs.*') ? 'active' : '' }}">PPh 21 TER</a></li>
                <li><a href="{{ route('finance::tax.income-taxs.index') }}" class="{{ Request::routeIs('finance::tax.income-taxs.*') ? 'active' : '' }}">Pasal 17</a></li>
                <li><a href="{{ route('finance::payroll.validations.index') }}" class="{{ Request::routeIs('finance::payroll.validations.*') ? 'active' : '' }}">Penerbitan</a></li>
            </ul>
        </li>

        <li class="menu-title" key="t-report">Pelaporan</li>
        <li class="{{ Request::routeIs('finance::report.salaries.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::report.salaries.index') }}" class="waves-effect {{ Request::routeIs('finance::report.salaries.*') ? 'active' : '' }}">
                <i class="bx bx-file"></i>
                <span>Penggajian</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('finance::report.overtimes.*') ? 'mm-active' : '' }}">
            <a href="{{ route('finance::report.overtimes.index') }}" class="waves-effect {{ Request::routeIs('finance::report.overtimes.*') ? 'active' : '' }}">
                <i class="bx bx-time"></i>
                <span>Lembur</span>
            </a>
        </li>

        <li class="menu-title" key="t-tax">Pajak</li>
        <li class="{{ Request::routeIs('finance::tax.*') && !Request::routeIs('finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'mm-active' : '' }}">
            <a href="javascript: void(0);" class="has-arrow waves-effect {{ Request::routeIs('finance::tax.*') && !Request::routeIs('finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'active' : '' }}">
                <i class="bx bx-coin-stack"></i>
                <span key="t-perpajakan">Perpajakan</span>
            </a>
            <ul class="sub-menu mm-collapse {{ Request::routeIs('finance::tax.*') && !Request::routeIs('finance::tax.ter-taxs.*', 'finance::tax.income-taxs.*') ? 'mm-show' : '' }}" aria-expanded="false">
                <li><a href="{{ route('finance::tax.incomes.index') }}" class="{{ Request::routeIs('finance::tax.incomes.*') ? 'active' : '' }}">Bukti Potong PPh 21</a></li>
                <li><a href="{{ route('finance::tax.employeetaxs.index') }}" class="{{ Request::routeIs('finance::tax.employeetaxs.*') ? 'active' : '' }}">Informasi Wajib Pajak</a></li>
                <li><a href="{{ route('finance::tax.ptkps.index') }}" class="{{ Request::routeIs('finance::tax.ptkps.*') ? 'active' : '' }}">Referensi PTKP</a></li>
                <li><a href="{{ route('finance::tax.templates.index') }}" class="{{ Request::routeIs('finance::tax.templates.*') ? 'active' : '' }}">Template PPh 21</a></li>
            </ul>
        </li>
    </ul>
</div>
