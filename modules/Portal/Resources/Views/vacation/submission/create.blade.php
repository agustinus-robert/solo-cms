@extends('portal::layouts.index')

@section('title', 'Buat Pengajuan Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

@include('components.tourguide', [
    'steps' => array_filter([
        ['selector' => '.tg-steps-vacation-category', 'title' => 'Jenis cuti/libur hari raya', 'content' => 'Pilih jenis cuti atau libur hari raya yang sesuai dengan kebutuhan kamu.'],
        ['selector' => '.tg-steps-vacation-date', 'title' => 'Tanggal cuti', 'content' => 'Kolom ini diisi tanggal cuti yang udah kamu rencanain.'],
        ['selector' => '.tg-steps-vacation-description', 'title' => 'Keperluan cuti', 'content' => 'Bisa diisi keperluan, catatan, alasan, atau deskripsi penting lainnya kalau ada.'],
    ]),
])

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
        .category-item {
            border: 1px solid #eff2f7;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .category-item:hover {
            background-color: #f8f9fa;
            border-color: #3b5de7;
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

                {{-- Breadcrumb & Header --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Form Pengajuan Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">Silakan tentukan tanggal dan jenis cuti Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        <strong>Mohon periksa kembali:</strong>
                        <ul class="mb-0 mt-1 font-size-13">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4 p-md-5">
                                <form class="form-confirm form-block" action="{{ route('portal::vacation.submission.store') }}" method="post">
                                    @csrf

                                    {{-- Section 1: Jenis Cuti --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-format-list-bulleted-type me-1"></i> Jenis Cuti
                                            </label>
                                            <p class="text-muted font-size-11">Pilih kategori cuti atau libur hari raya yang tersedia untuk Anda.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div class="tg-steps-vacation-category">
                                                @foreach ($quotas->groupBy(fn($quota) => $quota->category->type->label()) as $type => $_quotas)
                                                    <div class="mb-3">
                                                        <h6 class="font-size-12 fw-bold text-uppercase text-muted mb-2 d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($type) }}" style="cursor: pointer;">
                                                            {{ $type }} <i class="mdi mdi-chevron-down ms-auto"></i>
                                                        </h6>
                                                        <div class="collapse {{ $_quotas->first()->category->type->quotaVisibility() == true ? 'show' : '' }}" id="collapse-{{ Str::slug($type) }}">
                                                            @foreach ($_quotas as $quota)
                                                                @php($is_remain = !is_null($quota->quota) && $quota->remain <= 0)
                                                                <div class="category-item p-3 mb-2 d-flex align-items-center {{ $is_remain ? 'bg-light opacity-50' : '' }}">
                                                                    <div class="form-check mb-0">
                                                                        <input class="form-check-input" type="radio" name="quota_id" id="q-{{ $quota->id }}" data-meta="{{ json_encode($quota->category->meta) }}" value="{{ $quota->id }}" data-quota="{{ !is_null($quota->quota ?? null) ? $quota->remain : -1 }}" data-start="{{ $quota->start_at <= now() ? now()->format('Y-m-d') : $quota->start_at->format('Y-m-d') }}" @if ($is_remain) disabled @endif required>
                                                                        <label class="form-check-label ms-2 cursor-pointer" for="q-{{ $quota->id }}">
                                                                            <span class="d-block fw-bold text-dark font-size-14">{{ $quota->category->name }}</span>
                                                                            <span class="text-muted font-size-11">
                                                                                Sisa: <span class="text-primary fw-bold">{{ is_null($quota->quota) ? '∞' : ($quota->remain <= 0 ? 0 : $quota->remain) }} Hari</span>
                                                                                <span class="mx-1">|</span> Berakhir: {{ $quota->end_at->format('d M Y') }}
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 2: Tanggal --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-calendar-range me-1"></i> Waktu Cuti
                                            </label>
                                            <p class="text-muted font-size-11">Tentukan tanggal mulai dan berakhirnya cuti Anda.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            {{-- Field Mode: Options --}}
                                            <div class="inputs-meta-fields tg-steps-vacation-date" id="inputs-options">
                                                <div id="fields-options-tbody">
                                                    <div class="d-flex align-items-center mb-2" id="fields-options-template">
                                                        <div class="flex-grow-1">
                                                            <div class="input-group input-group-sm">
                                                                <input type="date" class="form-control" name="dates[]" min="{{ date('Y-m-d') }}">
                                                                <div class="input-group-text bg-light inputs-meta-as_freelances d-none">
                                                                    <div class="form-check mb-0">
                                                                        <input class="form-check-input mt-0" name="as_freelances[]" type="checkbox" value="1" onchange="toggleCheckbox(event)">
                                                                        <span class="ms-1 font-size-11 fw-bold">Freelance</span>
                                                                    </div>
                                                                    <input class="form-check-input d-none unchecked" name="as_freelances[]" type="checkbox" value="0" checked="checked">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-sm btn-soft-danger ms-2 btn-delete d-none" type="button" onclick="removeRow(event)">
                                                            <i class="mdi mdi-delete-outline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="inputs-meta-as_freelances-text" class="text-muted font-size-10 mb-2 d-none">
                                                    * Centang <b>Freelance</b> jika Anda tetap bekerja remote pada tanggal tersebut.
                                                </div>
                                                <button id="fields-options-add" type="button" class="btn btn-sm btn-soft-primary waves-effect mt-2 disabled">
                                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Hari
                                                </button>
                                            </div>

                                            {{-- Field Mode: Range --}}
                                            <div class="inputs-meta-fields d-none" id="inputs-range">
                                                <div class="input-group input-group-sm">
                                                    <input id="inputs-range-from" type="date" class="form-control" onchange="changeMinDateOfRangeEndAt(event)">
                                                    <span class="input-group-text bg-light">s.d.</span>
                                                    <input id="inputs-range-to" type="date" class="form-control" onchange="createDateRange()">
                                                </div>
                                                <div id="inputs-range-dates-group"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 3: Deskripsi --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-comment-text-outline me-1"></i> Keperluan
                                            </label>
                                            <p class="text-muted font-size-11">Berikan alasan singkat pengambilan cuti Anda.</p>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4 tg-steps-vacation-description">
                                            <textarea class="form-control font-size-13" name="description" rows="4" placeholder="Contoh: Acara keluarga di luar kota..."></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-lg-9 offset-lg-3 ps-lg-4">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-lg px-5 waves-effect waves-light">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Ajukan Sekarang
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
        let quota = 0;
        let meta = {}

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('fields-options-add').addEventListener('click', addRow);
            [].slice.call(document.querySelectorAll('[name="quota_id"]')).map((e) => {
                e.addEventListener('click', renderFields);
            });
        });

        const renderFields = (e) => {
            if (e.target.dataset.meta) {
                meta = JSON.parse(e.target.dataset.meta);
                quota = JSON.parse(e.target.dataset.quota);
                start = e.target.dataset.start;
                quota = quota < 0 ? 365 : quota;

                Array.from(tbody.children).map((el, i) => { if (i > 0) el.remove(); })
                Array.from(document.querySelectorAll('.inputs-range-dates')).map((el) => el.remove());

                document.querySelector('#inputs-range-from').value = '';
                document.querySelector('#inputs-range-to').value = '';
                document.querySelector('#inputs-range-from').required = meta.fields == 'range';
                document.querySelector('#inputs-range-to').required = meta.fields == 'range';
                document.querySelector('#inputs-options input').required = meta.fields == 'options';

                Array.from(document.querySelectorAll('.inputs-meta-fields')).map((el) => el.classList.add('d-none'));
                document.querySelector(`#inputs-${meta.fields}`).classList.remove('d-none');

                Array.from(document.querySelectorAll('.inputs-meta-as_freelances')).map((el) => el.classList.toggle('d-none', !meta.as_freelance));
                document.querySelector('#inputs-meta-as_freelances-text').classList.toggle('d-none', !meta.as_freelance);

                Array.from(document.querySelectorAll('[name="dates[]"]')).map((el) => {
                    el.value = '';
                    el.setAttribute('min', start);
                });

                toggleAddButtonBasedQuota();
            }
        }

        const toggleAddButtonBasedQuota = () => {
            const btn = document.getElementById('fields-options-add');
            const canAdd = tbody.children.length < quota;
            btn.classList.toggle('disabled', !canAdd);
        }

        const addRow = () => {
            if (tbody.children.length < quota) {
                let firstRow = document.querySelector('#fields-options-template');
                let newRow = firstRow.cloneNode(true);
                newRow.removeAttribute('id');
                newRow.querySelector('input[type="date"]').value = '';
                newRow.querySelector('.btn-delete').classList.remove('d-none');
                newRow.querySelector('.inputs-meta-as_freelances').classList.toggle('d-none', !meta.as_freelance);
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
            if (quota >= 0) {
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
