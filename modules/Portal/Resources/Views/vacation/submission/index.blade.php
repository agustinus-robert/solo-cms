@extends('portal::layouts.index')

@section('title', 'Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

@include('components.tourguide', [
    'steps' => array_filter([
        [
            'selector' => '.tg-steps-vacation-submission',
            'title' => 'Pengajuan cuti',
            'content' => 'Tekan tombol ini untuk melakukan pengajuan cuti.',
        ],
        [
            'selector' => '.tg-steps-vacation-quota',
            'title' => 'Kuota cuti',
            'content' => 'Kolom ini menampilkan daftar jatah cuti yang Anda miliki di tahun yang sudah ditentukan.',
        ],
        [
            'selector' => '.tg-steps-vacation-filter',
            'title' => 'Filter riwayat cuti',
            'content' => 'Gunakan filter ini untuk melihat riwayat cuti pada bulan-bulan sebelumnya.',
        ],
        [
            'selector' => '.tg-steps-vacation-table',
            'title' => 'Tabel riwayat cuti',
            'content' => 'Menampilkan riwayat cuti berdasarkan filter yang diterapkan.',
        ],
    ]),
])

@section('contents')
    {{-- Header Topbar - Jangan Sampai Ketinggalan --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm"><img src="{{ asset('skote/images/logo.svg') }}" height="22"></span>
                        <span class="logo-lg"><img src="{{ asset('skote/images/logo-dark.png') }}" height="17"></span>
                    </a>
                </div>
                <button type="button" class="btn btn-sm font-size-16 d-lg-none header-item waves-effect waves-light px-3" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            </div>

            <div class="d-flex">
                @include('layouts.nav-dashboard')
                @include('layouts.shortcut_menu')
                <div class="dropdown d-none d-lg-inline-block ms-1">
                    @include('layouts.nav_name')
                </div>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Page Title & Header --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Cuti & Libur</h4>
                                <p class="text-muted mb-0 font-size-13">Kelola jatah dan pantau riwayat pengajuan cuti Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if (Session::has('success') || Session::has('danger'))
                    <div class="alert {{ Session::has('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi {{ Session::has('success') ? 'mdi-check-all' : 'mdi-block-helper' }} me-2"></i>
                        {{ Session::get('success') ?? Session::get('danger') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Sisi Kiri: Aksi & Sisa Kuota --}}
                    <div class="col-xl-4">
                        {{-- Card Tombol Ajukan --}}
                        <div class="card border-0 shadow-sm mb-4 tg-steps-vacation-submission overflow-hidden" style="border-radius: 15px;">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title rounded-circle">
                                        <i class="mdi mdi-calendar-plus text-white font-size-24"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold">Ajukan Cuti Baru</h5>
                                <p class="text-muted font-size-13 px-3 mb-4">Butuh waktu istirahat? Klik tombol di bawah untuk memulai pengajuan.</p>

                                @if (count($quotas))
                                    <a href="{{ route('portal::vacation.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary waves-effect waves-light w-100 py-2">
                                        <i class="mdi mdi-airplane-takeoff me-1"></i> Mulai Pengajuan
                                    </a>
                                @else
                                    <button class="btn btn-secondary disabled w-100 py-2">Kuota Habis/Kosong</button>
                                @endif
                            </div>
                        </div>

                        {{-- Card Detail Kuota --}}
                        <div class="card border-0 shadow-sm mb-4 tg-steps-vacation-quota" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0 fw-bold text-primary"><i class="mdi mdi-chart-donut me-1"></i> Sisa Jatah Cuti</h6>
                            </div>
                            <div class="card-body p-0">
                                @forelse($quotas as $quota)
                                    <div class="d-flex align-items-center p-3 border-bottom border-light">
                                        <div class="flex-grow-1">
                                            <span class="badge badge-soft-info text-uppercase font-size-10 mb-1">{{ $quota->category->name }}</span>
                                            <p class="text-muted font-size-11 mb-0 italic">Berlaku s.d {{ $quota->end_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-end">
                                            <h4 class="mb-0 fw-bolder text-dark">{{ $quota->remain }}</h4>
                                            <small class="text-muted font-size-10 text-uppercase">Hari</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-calendar-remove font-size-24 text-muted opacity-50"></i>
                                        <p class="text-muted font-size-12 mb-0">Belum ada jatah cuti.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Tabel Riwayat --}}
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold"><i class="mdi mdi-history me-1"></i> Riwayat Pengajuan</h6>
                                <button class="btn btn-sm btn-soft-secondary font-size-11" type="button" data-bs-toggle="collapse" data-bs-target="#filter-collapse">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                                </button>
                            </div>

                            {{-- Filter --}}
                            <div class="collapse @if (request('search')) show @endif tg-steps-vacation-filter" id="filter-collapse">
                                <div class="card-body bg-light bg-opacity-25 border-bottom">
                                    <form action="{{ route('portal::vacation.submission.index') }}" method="get" class="row g-2">
                                        <div class="col-md-5">
                                            <input class="form-control form-control-sm" type="search" name="search" placeholder="Cari keterangan..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control" type="date" name="start_at" value="{{ request('start_at') }}">
                                                <span class="input-group-text bg-white">-</span>
                                                <input class="form-control" type="date" name="end_at" value="{{ request('end_at') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex gap-1">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="mdi mdi-magnify"></i></button>
                                            <a href="{{ route('portal::vacation.submission.index') }}" class="btn btn-soft-secondary btn-sm"><i class="mdi mdi-refresh"></i></a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive tg-steps-vacation-table">
                                    <table class="table table-nowrap align-middle mb-0">
                                        <thead class="table-light font-size-11 text-uppercase text-muted">
                                            <tr>
                                                <th class="ps-4">Detail Pengajuan</th>
                                                <th>Jadwal Cuti</th>
                                                <th class="text-center">Status</th>
                                                <th class="pe-4 text-end">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($vacations as $vacation)
                                                <tr @class(['opacity-50' => $vacation->trashed()])>
                                                    <td class="ps-4">
                                                        <h6 class="font-size-13 mb-1 text-dark">{{ $vacation->quota->category->name }}</h6>
                                                        <p class="text-muted font-size-12 text-truncate mb-1" style="max-width: 250px;">{{ $vacation->description }}</p>
                                                        <small class="text-primary font-size-11 fw-bold">Diajukan: {{ $vacation->created_at->format('d/m/Y') }}</small>
                                                    </td>
                                                    <td>
                                                        @isset(collect($vacation->dates)->first()['cashable'])
                                                            <span class="badge bg-soft-dark text-dark border font-size-11">{{ collect($vacation->dates)->count() }} Hari Kompensasi</span>
                                                        @else
                                                            <div class="d-flex flex-column gap-1">
                                                                @foreach (collect($vacation->dates)->take(1) as $date)
                                                                    <div class="font-size-12 fw-medium text-dark">
                                                                        <i class="mdi mdi-calendar-check text-success me-1"></i>
                                                                        {{ date('d M Y', strtotime($date['d'])) }}
                                                                    </div>
                                                                @endforeach
                                                                @php($remain = collect($vacation->dates)->count() - 1)
                                                                @if ($remain > 0)
                                                                    <span class="text-muted font-size-10">+{{ $remain }} hari lainnya</span>
                                                                @endif
                                                            </div>
                                                        @endisset
                                                    </td>
                                                    <td class="text-center">
                                                        @include('portal::vacation.components.status', ['vacation' => $vacation])
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-light dropdown-toggle font-size-16 p-1" type="button" data-bs-toggle="dropdown">
                                                                <i class="mdi mdi-dots-horizontal"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                                                <li><a class="dropdown-item" href="{{ route('portal::vacation.submission.show', ['vacation' => $vacation->id, 'next' => request('next')]) }}"><i class="mdi mdi-eye-outline text-primary me-2"></i> Detail</a></li>
                                                                <li><a class="dropdown-item" href="{{ route('portal::vacation.print', ['vacation' => $vacation->id]) }}" target="_blank"><i class="mdi mdi-printer-outline text-dark me-2"></i> Cetak</a></li>

                                                                @if ($vacation->can('revised'))
                                                                    <li><a class="dropdown-item text-info" href="{{ route('portal::vacation.submission.edit', ['vacation' => $vacation->id, 'next' => request('next')]) }}"><i class="mdi mdi-square-edit-outline me-2"></i> Ubah</a></li>
                                                                @endif

                                                                @if ($vacation->can('deleted'))
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form class="form-confirm" action="{{ route('portal::vacation.submission.destroy', ['vacation' => $vacation->id]) }}" method="post">
                                                                            @csrf @method('delete')
                                                                            <button type="submit" class="dropdown-item text-danger"><i class="mdi mdi-trash-can-outline me-2"></i> Batalkan</button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center py-5">@include('components.notfound')</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-top py-3">
                                {{ $vacations->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script src="{{ asset('js/vendor/daterangepicker.js') }}"></script>
@endpush
