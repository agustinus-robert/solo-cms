@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Kegiatan | ' . env('APP_NAME'))

@section('navtitle', 'Insentif')

@section('contents')
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
                    <div class="col-xl-9">
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

                                <div class="mt-5">
                                    <h6 class="text-muted font-size-11 text-uppercase fw-bold mb-3">Alur Persetujuan</h6>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light text-uppercase font-size-11">
                                                <tr>
                                                    <th style="width: 250px;">Penanggungjawab</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Status</th>
                                                    <th>Tindakan / Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $allApprovals = $outwork->approvables->sortBy('level');
                                                    $myPositionIds = $employee->positions()->pluck('id')->toArray();

                                                    $currentActiveId = null;
                                                    $pendingOnes = $outwork->approvables->where('result', 0)->sortByDesc('level');
                                                    if($pendingOnes->count() > 0) {
                                                        $currentActiveId = $pendingOnes->first()->id;
                                                    }
                                                @endphp
                                                @foreach ($allApprovals as $approvable)
                                                    @php
                                                        $isMyTurn = ($approvable->id === $currentActiveId) && in_array($approvable->userable_id, $myPositionIds);
                                                        $statusValue = is_object($approvable->result) ? $approvable->result->value : $approvable->result;
                                                    @endphp
                                                    <tr class="{{ $isMyTurn ? 'table-warning table-light' : '' }}">
                                                        <td>
                                                            <span class="d-block fw-bold text-dark">{{ $approvable->userable->employee->user->name ?? '?' }}</span>
                                                            <small class="text-muted">{{ $approvable->userable->position->name ?? '-' }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-soft-secondary">Lv. {{ $approvable->level }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-{{ $approvable->result->color() }}">{{ $approvable->result->label() }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($isMyTurn && !$outwork->trashed())
                                                                <form action="{{ route('portal::outwork.manage.update', $approvable->id) }}" method="post" class="row gx-2 gy-1 align-items-center">
                                                                    @csrf @method('put')
                                                                    <input type="hidden" name="approvable_id" value="{{ $approvable->id }}">
                                                                    <input type="hidden" name="next" value="{{ url()->current() }}">
                                                                    <div class="col-sm-7">
                                                                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="Catatan...">
                                                                    </div>
                                                                    <div class="col-sm-5">
                                                                        <div class="btn-group btn-group-sm w-100">
                                                                            <button type="submit" name="result" value="1" class="btn btn-success fw-bold">Setuju</button>
                                                                            <button type="submit" name="result" value="2" class="btn btn-danger fw-bold">Tolak</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @else
                                                                <span class="text-muted font-size-12 italic">{{ $approvable->reason ?: '-' }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24 fw-bold">
                                        {{ substr($outwork->employee->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $outwork->employee->user->name }}</h6>
                                <p class="text-muted font-size-12 mb-3">{{ $outwork->employee->position->position->name ?? '-' }}</p>
                                <div class="text-start bg-light p-2 rounded">
                                    <small class="text-muted d-block font-size-10 text-uppercase fw-bold">Departemen</small>
                                    <p class="text-dark font-size-12 mb-0">{{ $outwork->employee->position->position->department->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
