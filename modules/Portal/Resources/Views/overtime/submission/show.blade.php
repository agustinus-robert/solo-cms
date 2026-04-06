@extends('portal::layouts.index')

@section('title', 'Detail Lembur | ' . env('APP_NAME'))

@section('navtitle', 'Lembur')

@section('contents')
    {{-- Header Topbar --}}
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

                {{-- Header Halaman --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::overtime.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Detail Pengajuan Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">Informasi lengkap rincian dan status lembur.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($overtime->trashed())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian!</strong> Pengajuan ini telah dihapus. Anda tidak dapat mengelola data ini lagi.
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        {{-- Card Detail Informasi --}}
                        @if ($overtime->dates || is_null($overtime->accepted_at))
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-4 text-primary"><i class="mdi mdi-information-outline me-1"></i> Data Pengajuan</h5>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <p class="text-muted font-size-12 mb-1">Nama Kegiatan</p>
                                            <h6 class="fw-bold text-dark">{{ $overtime->name }}</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted font-size-12 mb-1">Tanggal Pengajuan</p>
                                            <h6 class="fw-bold">{{ $overtime->created_at->formatLocalized('%A, %d %B %Y') }}</h6>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-muted font-size-12 mb-2">Jadwal yang Diajukan</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($overtime->schedules)
                                                @foreach ($overtime->schedules as $date)
                                                    <span class="badge badge-soft-secondary font-size-12 p-2 border border-secondary border-opacity-10">
                                                        <i class="mdi mdi-calendar-clock me-1"></i>
                                                        {{ strftime('%d %B %Y', strtotime($date['d'])) }}
                                                        <span class="text-muted mx-1">|</span>
                                                        {{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    @if($overtime->dates)
                                    <div class="mb-4">
                                        <p class="text-muted font-size-12 mb-2">Realisasi Pelaksanaan</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($overtime->dates as $date)
                                                <span class="badge badge-soft-success font-size-12 p-2 border border-success border-opacity-10">
                                                    <i class="mdi mdi-check-decagram me-1"></i>
                                                    {{ strftime('%d %B %Y', strtotime($date['d'])) }}
                                                    <span class="text-muted mx-1">|</span>
                                                    {{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-4">
                                        <p class="text-muted font-size-12 mb-1">Deskripsi / Catatan</p>
                                        <div class="p-3 bg-light rounded text-dark font-size-13 border border-dashed">
                                            {{ $overtime->description ?: 'Tidak ada deskripsi tambahan.' }}
                                        </div>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col-sm-6">
                                            <p class="text-muted font-size-12 mb-1">Status Saat Ini</p>
                                            @include('portal::overtime.components.status', ['overtime' => $overtime])
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="text-muted font-size-12 mb-1">Lampiran</p>
                                            @if (isset($overtime->attachment) && Storage::exists($overtime->attachment))
                                                <a href="{{ Storage::url($overtime->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info py-1">
                                                    <i class="mdi mdi-file-document-outline me-1"></i> Buka Lampiran
                                                </a>
                                            @else
                                                <span class="text-muted font-size-13 italic">Tidak ada berkas</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Alur Persetujuan --}}
                                @if ($overtime->approvables->count())
                                    <div class="card-footer bg-transparent border-top p-4">
                                        <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-4">Riwayat Persetujuan Atasan</h6>
                                        <div class="row g-4">
                                            @foreach ($overtime->approvables as $approvable)
                                                <div class="col-md-6 border-start border-2 border-light ps-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted font-size-11">{{ ucfirst($approvable->type) }} Level {{ $approvable->level }}</span>
                                                        <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-10">{{ $approvable->result->label() }}</span>
                                                    </div>
                                                    <h6 class="mb-1 font-size-14">{{ $approvable->userable->getApproverLabel() }}</h6>
                                                    @if($approvable->reason)
                                                        <p class="text-muted font-size-12 italic mb-0">"{{ $approvable->reason }}"</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Form Realisasi (Jika Belum Isi Realisasi & Sudah Diterima) --}}
                        @if (!$overtime->dates && $overtime->accepted_at)
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-4 text-danger"><i class="mdi mdi-pencil-box-outline me-1"></i> Pengisian Realisasi Lembur</h5>
                                    <form class="form-confirm form-block" action="{{ route('portal::overtime.submission.update', ['overtime' => $overtime->id]) }}" method="post" enctype="multipart/form-data">
                                        @csrf @method('put')

                                        <div class="row mb-3">
                                            <div class="col-12 tg-steps-overtime-name">
                                                <label class="form-label">Nama Pekerjaan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $overtime->name) }}" required>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Waktu Realisasi <span class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm btn-soft-primary btn-add"><i class="mdi mdi-plus"></i></button>
                                            </div>
                                            <div class="tg-steps-overtime-dates" id="dates">
                                                {{-- Row template akan dirender oleh JS --}}
                                            </div>
                                        </div>

                                        <div class="row mb-3 tg-steps-overtime-description">
                                            <div class="col-12">
                                                <label class="form-label">Deskripsi Hasil Kerja</label>
                                                <textarea class="form-control" name="description" rows="4" placeholder="Tulis rincian hasil pekerjaan lembur...">{{ old('description') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-4 tg-steps-overtime-attachment">
                                            <div class="col-12">
                                                <label class="form-label">Lampiran Berkas (Opsional)</label>
                                                <input class="form-control" name="attachment" type="file" accept="image/*,application/pdf">
                                                <small class="text-muted">JPG, PNG, atau PDF (Maks. 2MB)</small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                            <a class="btn btn-light px-4" href="{{ request('next', route('portal::overtime.submission.index')) }}">Kembali</a>
                                            <button class="btn btn-primary px-5 waves-effect waves-light"><i class="mdi mdi-send me-1"></i> Ajukan Realisasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-xl-4">
                        {{-- Info Karyawan --}}
                        @include('portal::components.employee-detail-card', ['employee' => $overtime->employee])

                        {{-- Tombol Aksi --}}
                        <div class="mt-4">
                            @if ($overtime->empl_id == Auth::user()->employee->id && is_null($overtime->accepted_at))
                                <form class="form-block form-confirm mb-3" action="{{ route('portal::overtime.submission.approve', ['overtime' => $overtime->id]) }}" method="post">
                                    @csrf @method('put')
                                    <button class="btn btn-soft-success w-100 py-3 text-start shadow-sm border-success border-opacity-10 position-relative">
                                        <i class="mdi mdi-check-circle-outline mdi-24px float-end text-success opacity-25"></i>
                                        <h6 class="text-success mb-1">Terima Instruksi</h6>
                                        <p class="text-muted font-size-11 mb-0">Terima penugasan lembur dari atasan.</p>
                                    </button>
                                </form>
                            @endif

                            @unless ($overtime->hasAnyApprovableResultIn('REJECT') || !$overtime->hasApprovables() || $overtime->trashed())
                                @if ($overtime->hasAllApprovableResultIn('PENDING') || $overtime->hasAnyApprovableResultIn('REVISION'))
                                    <form class="form-block form-confirm" action="{{ route('portal::overtime.submission.destroy', ['overtime' => $overtime->id]) }}" method="post">
                                        @csrf @method('delete')
                                        <button class="btn btn-soft-danger w-100 py-3 text-start border-danger border-opacity-10 position-relative">
                                            <i class="mdi mdi-delete-outline mdi-24px float-end text-danger opacity-25"></i>
                                            <h6 class="text-danger mb-1">Batalkan Pengajuan</h6>
                                            <p class="text-muted font-size-11 mb-0">Hapus data sebelum diverifikasi atasan.</p>
                                        </button>
                                    </form>
                                @endif
                            @endunless
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Template Row Realisasi --}}
    <div id="dates-template" class="d-none">
        <div class="date-row p-3 bg-light rounded mb-2 border border-dashed position-relative">
            <button type="button" class="btn btn-sm btn-soft-danger rounded-circle position-absolute" style="top: -10px; right: -10px;" onclick="removeAttachment(event)">
                <i class="mdi mdi-close"></i>
            </button>
            <div class="row g-2 dates-input">
                <div class="col-sm-5">
                    <input type="date" class="form-control form-control-sm" name="dates[d][]" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-sm-7">
                    <div class="input-group input-group-sm">
                        <input type="time" class="form-control" name="dates[s][]" onchange="changeMinTime(event)" required>
                        <span class="input-group-text bg-white">s.d.</span>
                        <input type="time" class="form-control" name="dates[e][]" onchange="changeMaxTime(event)" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const max_dates = 5;
        const schedules = @JSON($overtime->schedules);

        let removeAttachment = (e) => {
            e.currentTarget.closest('.date-row').remove();
            document.querySelector('#dates .btn-add').classList.toggle('disabled', document.getElementById('dates').querySelectorAll('.dates-input').length > max_dates);
        }

        let changeMinTime = (e) => {
            let endTimeInput = e.target.parentNode.querySelector('input[name="dates[e][]"]');
            if(endTimeInput) endTimeInput.min = e.target.value;
        }

        let changeMaxTime = (e) => {
            let startTimeInput = e.target.parentNode.querySelector('input[name="dates[s][]"]');
            if(startTimeInput) startTimeInput.max = e.target.value;
        }

        const addRow = (e = null) => {
            const datesContainer = document.getElementById('dates');
            if (datesContainer.querySelectorAll('.dates-input').length < max_dates) {
                datesContainer.insertAdjacentHTML('beforeend', document.getElementById('dates-template').innerHTML);
            }
        };

        const renderSchedule = () => {
            const datesContainer = document.getElementById('dates');
            if(!datesContainer) return;

            for (let i = 0; i < (schedules ? schedules.length : 1); i++) {
                addRow();
            }

            if(schedules) {
                ['d', 's', 'e'].forEach((key, keyIndex) => {
                    datesContainer.querySelectorAll(`[name="dates[${key}][]"]`).forEach((element, index) => {
                        if(schedules[index]) {
                            element.value = keyIndex === 0 ? schedules[index].d : keyIndex === 1 ? schedules[index].t_s : schedules[index].t_e;
                        }
                    });
                });
            }
        };

        window.addEventListener('DOMContentLoaded', () => {
            renderSchedule();
            const addButton = document.querySelector('.btn-add');
            if(addButton) {
                addButton.addEventListener('click', (e) => addRow(e));
            }
        });
    </script>
@endpush
