@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

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
                <button type="button" class="btn btn-sm font-size-16 d-lg-none header-item waves-effect waves-light px-3" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            </div>

            <div class="d-flex">
                @php
                    $user = auth()->user();
                @endphp

                @include('portal::layouts.components.notifications')
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

                {{-- Header Section --}}
                <div class="row align-items-center mb-4 mt-2">
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Informasi Pengajuan Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">Detail riwayat dan status pengajuan karyawan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($vacation->trashed())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian!</strong> Pengajuan ini telah dihapus. Anda tidak dapat lagi mengelola data ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="card-title mb-0 fw-bold text-primary">
                                        <i class="mdi mdi-text-box-search-outline me-1"></i> Rincian Pengajuan
                                    </h5>
                                    @if (!$vacation->trashed())
                                        <a class="btn btn-soft-success btn-sm waves-effect waves-light" href="{{ route('portal::vacation.print', ['vacation' => $vacation->id]) }}" target="_blank">
                                            <i class="mdi mdi-printer me-1"></i> Cetak PDF
                                        </a>
                                    @endif
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Tanggal Pengajuan</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $vacation->created_at->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Kategori Cuti</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $vacation->quota->category->name }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Daftar Tanggal Cuti</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @isset(collect($vacation->dates)->first()['cashable'])
                                                <span class="badge bg-dark px-3 py-2 fw-medium text-white">{{ $vacation->dates->count() }} Hari dikompensasikan</span>
                                            @else
                                                @foreach ($vacation->dates as $date)
                                                    <span class="badge bg-soft-secondary text-dark px-3 py-2 fw-medium {{ isset($date['c']) ? 'text-decoration-line-through' : '' }}" @isset($date['f']) data-bs-toggle="tooltip" title="Freelance Mode" @endisset>
                                                        @isset($date['f'])
                                                            <i class="mdi mdi-account-network text-danger me-1"></i>
                                                        @endisset
                                                        {{ \Carbon\Carbon::parse($date['d'])->translatedFormat('d M Y') }}
                                                    </span>
                                                @endforeach
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Alasan / Deskripsi</label>
                                        <p class="mb-0 text-dark">{{ $vacation->description ?: '-' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Status Saat Ini</label>
                                        <div>@include('portal::vacation.components.status', ['vacation' => $vacation])</div>
                                    </div>
                                </div>

                                @if ($vacation->approvables->count())
                                    <div class="mt-5">
                                        <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-3">Alur Persetujuan</h6>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle table-borderless bg-light rounded-3 overflow-hidden">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">Penanggungjawab</th>
                                                        <th>Level</th>
                                                        <th>Keputusan</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($vacation->approvables as $approvable)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="d-block fw-bold text-dark">{{ $approvable->userable->getApproverLabel() }}</span>
                                                                <small class="text-muted">{{ ucfirst($approvable->type) }}</small>
                                                            </td>
                                                            <td><span class="badge bg-soft-info text-info">Lv. {{ $approvable->level }}</span></td>
                                                            <td>
                                                                <span class="badge bg-{{ $approvable->result->color() }} text-white">
                                                                    <i class="{{ $approvable->result->icon() }} me-1"></i> {{ $approvable->result->label() }}
                                                                </span>
                                                            </td>
                                                            <td class="text-wrap" style="min-width: 200px;">
                                                                {{ $approvable->reason ?: '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        {{-- Logic Pencarian Approval --}}
                        @php
                            $myPositionIds = auth()->user()->employee->positions()->pluck('id')->toArray();

                            $myApprovals = $vacation->approvables->filter(function ($item) use ($myPositionIds) {
                                // Samakan namespace dengan hasil DD Robert
                                $isCorrectType = ($item->userable_type === 'Modules\HRMS\Models\EmployeePosition' ||
                                                $item->userable_type === \Modules\Portal\Models\EmployeePosition::class);

                                $isMyPosition = in_array($item->userable_id, $myPositionIds);

                                // Ambil value integer dari result
                                $statusValue = is_object($item->result) ? $item->result->value : $item->result;
                                $isPending = ($statusValue == 0);

                                return $isCorrectType && $isMyPosition && $isPending;
                            });

                            $hasDecisionByAnyone = $vacation->approvables->where('result', '!=', 0)->count() > 0;
                        @endphp

                        @forelse ($myApprovals as $approval)
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background-color: #fff9f0; border: 1px solid #ffeeba !important;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title rounded-circle bg-warning text-white">
                                                <i class="mdi mdi-account-check-outline"></i>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold mb-0">Konfirmasi Persetujuan</h6>
                                    </div>
                                    <p class="text-muted font-size-13 mb-3">
                                        Sebagai: <strong>{{ $approval->userable->getApproverLabel() }}</strong><br>
                                        Level: <span class="badge bg-soft-info text-info">Lv. {{ $approval->level }}</span>
                                    </p>

                                    <form action="{{ route('portal::vacation.manage.update', ['vacation' => $vacation->id]) }}" method="post" class="form-block">
                                        @csrf
                                        @method('put')

                                        <input type="hidden" name="next" value="{{ request('next') }}">

                                        <input type="hidden" name="approvable_id" value="{{ $approval->id }}">

                                        <div class="mb-3">
                                            <textarea class="form-control" name="reason" rows="2" placeholder="Tulis catatan (opsional)..."></textarea>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <button type="submit" name="result" value="1" class="btn btn-success w-100 waves-effect waves-light">
                                                    <i class="mdi mdi-check-all me-1"></i> Setuju
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" name="result" value="2" class="btn btn-danger w-100 waves-effect waves-light">
                                                    <i class="mdi mdi-close-circle-outline me-1"></i> Tolak
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                        @endforelse

                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4 fw-bold text-dark">
                                    <i class="mdi mdi-account-circle-outline me-1"></i> Data Karyawan
                                </h5>
                                <div class="list-group list-group-flush">
                                    @php
                                        $emp = $vacation->quota->employee;
                                        $pos = $emp->position->position ?? null;
                                    @endphp
                                    <div class="list-group-item px-0 py-2 border-light">
                                        <small class="text-muted d-block font-size-11 text-uppercase fw-bold">Nama Karyawan</small>
                                        <span class="text-dark fw-medium">{{ $emp->user->name }}</span>
                                    </div>
                                    <div class="list-group-item px-0 py-2 border-light">
                                        <small class="text-muted d-block font-size-11 text-uppercase fw-bold">NIP</small>
                                        <span class="text-dark fw-medium">{{ $emp->kd ?: '-' }}</span>
                                    </div>
                                    <div class="list-group-item px-0 py-2 border-light">
                                        <small class="text-muted d-block font-size-11 text-uppercase fw-bold">Jabatan</small>
                                        <span class="text-dark fw-medium">{{ $pos->name ?? '-' }}</span>
                                    </div>
                                    <div class="list-group-item px-0 py-2 border-light">
                                        <small class="text-muted d-block font-size-11 text-uppercase fw-bold">Departemen</small>
                                        <span class="text-dark fw-medium">{{ $pos->department->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-3">
                            {{-- Syarat: Tombol Batal hilang jika Robert sedang di posisi approve ATAU sudah ada atasan yang ambil tindakan --}}
                            @if ($vacation->can('deleted') && $myApprovals->isEmpty() && !$hasDecisionByAnyone)
                                <form class="form-block form-confirm mb-2" action="{{ route('portal::vacation.submission.destroy', ['vacation' => $vacation->id]) }}" method="post">
                                    @csrf @method('delete')
                                    <button type="submit" class="btn btn-light w-100 border text-start p-3 shadow-sm" style="border-radius: 10px;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-soft-danger text-danger font-size-18">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-danger fw-bold font-size-14">Batalkan Pengajuan</h6>
                                                <small class="text-muted">Hapus data pengajuan.</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
