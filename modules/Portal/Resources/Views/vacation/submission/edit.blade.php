@extends('portal::layouts.index')

@section('title', 'Ubah Pengajuan Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

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

    <style>
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .form-label-custom {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            color: #495057;
            font-weight: 700;
        }
        .category-item-static {
            border: 1px solid #eff2f7;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .divider-vertical {
            border-right: 1px solid #eff2f7;
        }
        @media (max-width: 991px) {
            .divider-vertical {
                border-right: none;
                border-bottom: 1px solid #eff2f7;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
            }
        }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Header & Back Button --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Ubah Pengajuan Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">Perbaiki data pengajuan Anda sesuai instruksi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Error Validation --}}
                @error('dates.*')
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror

                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4 p-md-5">
                                <form class="form-confirm form-block" action="{{ route('portal::vacation.submission.update', ['vacation' => $vacation->id]) }}" method="post">
                                    @csrf @method('PUT')

                                    {{-- Section 1: Jenis Cuti (Readonly) --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-tag-outline me-1"></i> Jenis Cuti
                                            </label>
                                            <p class="text-muted font-size-11">Kategori cuti tidak dapat diubah setelah diajukan.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div class="category-item-static p-3 d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <i class="mdi mdi-check-circle"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="font-size-14 mb-1 fw-bold text-dark">{{ $vacation->quota->category->name }}</h6>
                                                    <p class="text-muted font-size-11 mb-0">
                                                        Tipe: {{ $vacation->quota->category->type->label() }}
                                                        @php($remain = $vacation->quota->quota - $vacation->quota->vacations->sum(fn($v) => $v->dates->count()) + $vacation->dates->count())
                                                        <span class="mx-1">|</span> Sisa Kuota: <span class="text-primary fw-bold">{{ is_null($vacation->quota->quota) ? '∞' : ($remain <= 0 ? 0 : $remain) }} Hari</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <input type="hidden" data-meta="{{ json_encode($vacation->quota->category->meta) }}" data-quota="{{ !is_null($vacation->quota->quota ?? null) ? $remain : -1 }}">
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 2: Tanggal --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-calendar-edit me-1"></i> Waktu Cuti
                                            </label>
                                            <p class="text-muted font-size-11">Sesuaikan kembali tanggal yang ingin Anda ajukan.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            @if ($vacation->quota->category->meta->fields == 'options')
                                                <div class="inputs-meta-fields" id="inputs-options">
                                                    <div id="fields-options-tbody">
                                                        @foreach ($vacation->dates as $date)
                                                            <div class="d-flex align-items-center mb-2" @if ($loop->first) id="fields-options-template" @endif>
                                                                <div class="flex-grow-1">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="date" class="form-control" name="dates[]" value="{{ $date['d'] }}">
                                                                        <div class="input-group-text bg-light inputs-meta-as_freelances @if (!isset($vacation->quota->category->meta->as_freelance)) d-none @endif">
                                                                            <div class="form-check mb-0">
                                                                                <input class="form-check-input mt-0" name="as_freelances[]" type="checkbox" value="1" onchange="toggleCheckbox(event)" @isset($date['f']) checked="checked" @endisset>
                                                                                <span class="ms-1 font-size-11 fw-bold">Freelance</span>
                                                                            </div>
                                                                            <input class="form-check-input d-none unchecked" name="as_freelances[]" type="checkbox" value="0" @empty($date['f']) checked="checked" @endempty>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button class="btn btn-sm btn-soft-danger ms-2 btn-delete @if ($loop->first) d-none @endif" type="button" onclick="removeRow(event)">
                                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button id="fields-options-add" type="button" class="btn btn-sm btn-soft-primary mt-2 @if(!is_null($vacation->quota->quota) && $remain <= 0) disabled @endif">
                                                        <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Hari
                                                    </button>
                                                </div>
                                            @else
                                                <div class="inputs-meta-fields" id="inputs-range">
                                                    <div class="input-group input-group-sm">
                                                        <input id="inputs-range-from" type="date" class="form-control" onchange="changeMinDateOfRangeEndAt(event)" value="{{ $vacation->dates->first()['d'] }}">
                                                        <span class="input-group-text bg-light">s.d.</span>
                                                        <input id="inputs-range-to" type="date" class="form-control" onchange="createDateRange()" min="{{ $vacation->dates->first()['d'] }}" value="{{ $vacation->dates->last()['d'] }}">
                                                    </div>
                                                    <div id="inputs-range-dates-group">
                                                        @foreach ($vacation->dates as $date)
                                                            <input type="hidden" name="dates[]" class="inputs-range-dates" value="{{ $date['d'] }}">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 3: Keperluan --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-comment-text-outline me-1"></i> Keperluan
                                            </label>
                                            <p class="text-muted font-size-11">Berikan alasan atau keterangan perbaikan.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <textarea class="form-control font-size-13" name="description" rows="4" placeholder="Tulis alasan perbaikan cuti...">{{ $vacation->description }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="row mt-5">
                                        <div class="col-lg-9 offset-lg-3 ps-lg-4">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-lg px-5 waves-effect waves-light">
                                                    <i class="mdi mdi-sync me-1"></i> Simpan Perubahan
                                                </button>
                                                <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-light btn-lg px-4">
                                                    Batal
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const tbody = document.querySelector('#fields-options-tbody');
        let quota = @json($vacation->quota->quota);

        document.addEventListener('DOMContentLoaded', () => {
            let addButton = document.getElementById('fields-options-add');
            if (addButton) {
                addButton.addEventListener('click', addRow);
            }
        });

        const toggleAddButtonBasedQuota = () => {
            const btn = document.getElementById('fields-options-add');
            if(btn && quota !== null) {
                const canAdd = tbody.children.length < quota;
                btn.classList.toggle('disabled', !canAdd);
            }
        }

        const addRow = () => {
            let firstRow = document.querySelector('#fields-options-template');
            if (tbody.children.length < quota || quota === null) {
                let newRow = firstRow.cloneNode(true);
                newRow.removeAttribute('id');
                newRow.querySelector('input[type="date"]').value = '';
                newRow.querySelector('.btn-delete').classList.remove('d-none');

                let freelanceCheck = newRow.querySelector('[name="as_freelances[]"]');
                if(freelanceCheck) freelanceCheck.checked = false;

                let unchecked = newRow.querySelector('.unchecked');
                if(unchecked) unchecked.checked = true;

                tbody.appendChild(newRow);
            }
            toggleAddButtonBasedQuota();
        }

        const removeRow = (e) => {
            e.target.closest('.d-flex').remove();
            toggleAddButtonBasedQuota();
        }

        const toggleCheckbox = (el) => {
            let container = el.target.closest('.input-group-text');
            let checkboxes = container.querySelectorAll('[name="as_freelances[]"]');
            checkboxes[1].checked = !checkboxes[0].checked;
        }

        const changeMinDateOfRangeEndAt = (e) => {
            Array.from(document.querySelectorAll('.inputs-range-dates')).map((el) => el.remove());
            let end_at = document.querySelector('#inputs-range-to');
            end_at.value = '';
            end_at.min = e.target.value;

            if (quota >= 0 && quota !== null) {
                let max = new Date(e.target.value);
                max.setDate(max.getDate() + (quota - 1));
                end_at.max = max.toISOString().split('T')[0];
            }
        }

        const createDateRange = (e) => {
            let inputs = document.querySelectorAll('.inputs-range-dates');
            Array.from(inputs).map((el) => el.remove());
            let from = document.querySelector('#inputs-range-from').value;
            let to = document.querySelector('#inputs-range-to').value;
            if (from && to) {
                for (dt = new Date(from); dt <= new Date(to); dt.setDate(dt.getDate() + 1)) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'dates[]';
                    input.classList.add('inputs-range-dates');
                    input.value = (new Date(dt)).toISOString().split('T')[0]
                    document.getElementById('inputs-range-dates-group').appendChild(input)
                }
            }
        }
    </script>
@endpush
