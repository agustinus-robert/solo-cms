@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Kegiatan | ' . env('APP_NAME'))

@section('navtitle', 'Insentif')

@section('contents')
    {{-- Header Topbar --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="" class="logo logo-dark">
                        <span class="logo-sm"><img src="{{ asset('skote/images/logo.svg') }}" height="22"></span>
                        <span class="logo-lg"><img src="{{ asset('skote/images/logo-dark.png') }}" height="17"></span>
                    </a>
                </div>
            </div>
            <div class="d-flex">
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

                {{-- Breadcrumb & Title --}}
                <div class="row align-items-center mb-4 mt-2">
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::outwork.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Detail Laporan Kegiatan</h4>
                                <p class="text-muted mb-0 font-size-13">Pantau rincian kegiatan dan status persetujuan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Main Info --}}
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4 fw-bold text-primary">
                                    <i class="mdi mdi-text-box-search-outline me-1"></i> Rincian Laporan
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-11 text-uppercase fw-bold mb-1 d-block">Nama Kegiatan</label>
                                        <p class="mb-0 text-dark fw-bold font-size-15">{{ $outwork->name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-11 text-uppercase fw-bold mb-1 d-block">Kategori & Tanggal Lapor</label>
                                        <p class="mb-0 text-dark">
                                            <span class="badge bg-soft-info text-info">{{ $outwork->category->name }}</span>
                                            <small class="ms-2 text-muted">{{ $outwork->created_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-11 text-uppercase fw-bold mb-2 d-block">Jadwal Pelaksanaan</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light font-size-11">
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Waktu</th>
                                                        <th class="text-center">Persiapan?</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="font-size-13 text-dark">
                                                    @foreach ($outwork->dates as $date)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($date['d'])->translatedFormat('d F Y') }}</td>
                                                            <td>{{ $date['t_s'] }} - {{ $date['t_e'] }} <small class="text-muted">({{ $date['b'] }}m rest)</small></td>
                                                            <td class="text-center">{!! ($date['p'] ?? false) ? '<i class="mdi mdi-check-circle text-success"></i>' : '-' !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted font-size-11 text-uppercase fw-bold mb-1 d-block">Deskripsi</label>
                                        <p class="mb-0 text-dark bg-light p-3 rounded">{{ $outwork->description ?: '-' }}</p>
                                    </div>
                                </div>

                                {{-- Approval Tracking --}}
                                <div class="mt-5">
                                    <h6 class="text-muted font-size-11 text-uppercase fw-bold mb-3">Alur Persetujuan</h6>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="font-size-11">
                                                    <th>Penanggungjawab</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Status</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($outwork->approvables as $approvable)
                                                    <tr>
                                                        <td>
                                                            <span class="d-block fw-bold text-dark">{{ $approvable->userable->employee->user->name ?? '?' }}</span>
                                                            <small class="text-muted">{{ $approvable->userable->position->name ?? '-' }}</small>
                                                        </td>
                                                        <td class="text-center"><span class="badge badge-soft-secondary">Lv. {{ $approvable->level }}</span></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-{{ $approvable->result->color() }}">{{ $approvable->result->label() }}</span>
                                                        </td>
                                                        <td class="text-wrap font-size-12">{{ $approvable->reason ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar Action --}}
                    <div class="col-xl-4">
                        @php
                            $myPositionIds = $employee->positions()->pluck('id')->toArray();
                            $myApprovals = $outwork->approvables->filter(fn($item) => in_array($item->userable_id, $myPositionIds) && $item->result->value == 0);
                        @endphp

                        @foreach ($myApprovals as $approval)
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background-color: #fffdf1; border: 1px solid #ffeeba !important;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3 text-warning">
                                        <i class="mdi mdi-clipboard-check-outline font-size-24 me-2"></i>
                                        <h6 class="fw-bold mb-0">Persetujuan Anda</h6>
                                    </div>
                                    <form action="{{ route('portal::outwork.manage.update', $approval->id) }}" method="post" class="form-confirm">
                                        @csrf @method('put')
                                        <input type="hidden" name="approvable_id" value="{{ $approval->id }}">
                                        <div class="mb-3">
                                            <textarea class="form-control form-control-sm" name="reason" rows="2" placeholder="Tulis alasan/catatan jika perlu..."></textarea>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6"><button type="submit" name="result" value="1" class="btn btn-success btn-sm w-100 py-2">Setuju</button></div>
                                            <div class="col-6"><button type="submit" name="result" value="2" class="btn btn-danger btn-sm w-100 py-2">Tolak</button></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24 fw-bold">
                                        {{ substr($outwork->employee->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $outwork->employee->user->name }}</h6>
                                <p class="text-muted font-size-12">{{ $outwork->employee->position->position->name ?? '-' }}</p>
                                <hr class="my-3">
                                <div class="text-start">
                                    <small class="text-muted d-block font-size-11 text-uppercase fw-bold">Departemen</small>
                                    <p class="text-dark font-size-13">{{ $outwork->employee->position->position->department->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
