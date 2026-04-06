@extends('portal::layouts.index')

@section('title', 'Buat Pengajuan Izin | ' . env('APP_NAME'))

@section('contents')
    <style>
        .material-symbols-rounded { vertical-align: middle; }

        /* Card & Layout */
        .card-form { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .form-label-custom { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #495057; display: block; margin-bottom: 0.5rem; }

        /* Category Selection */
        .category-scroll { max-height: 380px; overflow-y: auto; padding-right: 8px; }
        .category-scroll::-webkit-scrollbar { width: 4px; }
        .category-scroll::-webkit-scrollbar-thumb { background: #e2e5ec; border-radius: 10px; }

        .category-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            margin-bottom: 10px;
            background: #fff;
            border: 1px solid #eff2f7;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .category-item:hover { border-color: #556ee6; background-color: #fbfbff; }

        .form-check-input { width: 1.2rem; height: 1.2rem; margin-top: 0; cursor: pointer; }
        .form-check-input:checked + .category-content .fw-bold { color: #556ee6; }
        .form-check-input:checked ~ .category-active-check { display: block !important; }

        /* File Upload */
        .upload-wrapper {
            border: 2px dashed #ced4da;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }
        .upload-wrapper:hover { border-color: #556ee6; background-color: #f1f4ff; }

        @media (min-width: 992px) {
            .divider-vertical { border-right: 1px solid #eff2f7; }
        }
    </style>

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('skote/images/logo.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('skote/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('skote/images/logo-light.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('skote/images/logo-light.png') }}" alt="" height="39">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm font-size-16 d-lg-none header-item waves-effect waves-light px-3" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

            </div>

            <div class="d-flex">

                <div class="dropdown d-inline-block d-lg-none ms-2">
                    <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">

                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Search input">

                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

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

                {{-- Breadcrumb/Header --}}
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::leave.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-1 fw-bold">Formulir Pengajuan Izin</h4>
                                <ol class="breadcrumb m-0 small">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Portal</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Izin</a></li>
                                    <li class="breadcrumb-item active">Buat Baru</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                @if (count($errors))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        <strong>Mohon periksa kembali:</strong>
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card card-form">
                            <div class="card-body p-4 p-lg-5">
                                <form class="form-confirm form-block" action="{{ route('portal::leave.submission.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        {{-- Kiri: Kategori --}}
                                        <div class="col-lg-5 divider-vertical pe-lg-4">
                                            <label class="form-label-custom">
                                                <i class="mdi mdi-format-list-bulleted-type text-primary me-1"></i> Pilih Jenis Izin
                                            </label>
                                            <p class="text-muted small mb-3">Pilih kategori yang paling sesuai dengan alasan ketidakhadiran Anda.</p>

                                            <div class="tg-steps-leave-category">
                                                <div class="category-scroll">
                                                    @forelse($categories as $category)
                                                        @if ($category->children->count())
                                                            <div class="text-primary fw-bold small text-uppercase mb-2 mt-3" style="letter-spacing: 1px;">{{ $category->name }}</div>
                                                            @foreach ($category->children as $child)
                                                                <label class="category-item">
                                                                    <input class="form-check-input me-3" type="radio" name="ctg_id" data-meta="{{ json_encode($child->meta) }}" value="{{ $child->id }}" data-quota="{{ $child->meta?->quota ?: -1 }}" required>
                                                                    <div class="category-content flex-grow-1">
                                                                        <div class="fw-bold text-dark font-size-14">{{ $child->name }}</div>
                                                                        <div class="text-muted font-size-11">Sisa Kuota: <span class="badge badge-soft-info">{{ $child->meta?->quota ?: '∞' }} hari</span></div>
                                                                    </div>
                                                                    <i class="mdi mdi-check-circle text-primary category-active-check d-none font-size-18"></i>
                                                                </label>
                                                            @endforeach
                                                        @else
                                                            <label class="category-item">
                                                                <input class="form-check-input me-3" type="radio" name="ctg_id" data-meta="{{ json_encode($category->meta) }}" value="{{ $category->id }}" data-quota="{{ $category->meta?->quota ?: -1 }}" required>
                                                                <div class="category-content flex-grow-1">
                                                                    <div class="fw-bold text-dark font-size-14">{{ $category->name }}</div>
                                                                    <div class="text-muted font-size-11">Sisa Kuota: <span class="badge badge-soft-info">{{ $category->meta?->quota ?: '∞' }} hari</span></div>
                                                                </div>
                                                                <i class="mdi mdi-check-circle text-primary category-active-check d-none font-size-18"></i>
                                                            </label>
                                                        @endif
                                                    @empty
                                                        <div class="text-center p-4 bg-light rounded">Data kategori tidak tersedia</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Kanan: Detail Waktu & Keterangan --}}
                                        <div class="col-lg-7 ps-lg-4 mt-4 mt-lg-0">

                                            {{-- Tanggal --}}
                                            <div class="mb-4 tg-steps-leave-date">
                                                <label class="form-label-custom"><i class="mdi mdi-calendar-range text-primary me-1"></i> Tanggal Rencana Izin</label>
                                                <div id="inputs-options">
                                                    <div id="fields-options-tbody">
                                                        <div class="d-flex gap-2 mb-2" id="fields-options-template">
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-light"><i class="mdi mdi-calendar"></i></span>
                                                                <input type="date" class="form-control" name="dates[]" min="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <button class="btn btn-soft-danger btn-delete d-none" type="button" onclick="removeRow(event)">
                                                                <i class="mdi mdi-trash-can-outline"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button id="fields-options-add" type="button" class="btn btn-link btn-sm text-primary p-0 mt-1 fw-bold disabled">
                                                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Tanggal Lain
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Waktu (Hidden by default) --}}
                                            <div class="mb-4 d-none" id="hide_if_date_only">
                                                <label class="form-label-custom"><i class="mdi mdi-clock-outline text-primary me-1"></i> Detail Jam Izin</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <input type="time" class="form-control" name="time_start">
                                                        <small class="text-muted">Jam Mulai</small>
                                                    </div>
                                                    <div class="col-6 hide_if_start_only">
                                                        <input type="time" class="form-control" name="time_end">
                                                        <small class="text-muted">Jam Selesai</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Keterangan --}}
                                            <div class="mb-4 tg-steps-leave-description">
                                                <label class="form-label-custom"><i class="mdi mdi-text-subject text-primary me-1"></i> Alasan / Keterangan</label>
                                                <textarea class="form-control" name="description" rows="4" placeholder="Tuliskan alasan pengajuan Anda secara jelas..."></textarea>
                                            </div>

                                            {{-- Lampiran --}}
                                            <div class="mb-4 tg-steps-leave-attachment">
                                                <label class="form-label-custom"><i class="mdi mdi-paperclip text-primary me-1"></i> Dokumen Pendukung (Opsional)</label>
                                                <div class="upload-wrapper">
                                                    <i class="mdi mdi-cloud-upload-outline display-4 text-light"></i>
                                                    <input class="form-control mt-2" name="attachment" type="file" id="upload-input" accept="image/*,application/pdf">
                                                    <p class="text-muted font-size-12 mt-2 mb-0">Format: JPG, PNG, atau PDF (Maks. 2MB)</p>
                                                </div>
                                            </div>

                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                                                <a href="{{ request('next', route('portal::leave.submission.index')) }}" class="btn btn-light btn-md px-4 me-md-2">Batal</a>
                                                <button type="submit" class="btn btn-primary btn-md px-5 shadow-primary">
                                                    <i class="mdi mdi-send me-1"></i> Kirim Pengajuan
                                                </button>
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
    {{-- JS script tetap sama dengan yang Anda miliki karena fungsinya sudah benar --}}
    <script>
        const tbody = document.querySelector('#fields-options-tbody');
        let quota = 0;
        let meta = {}

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('fields-options-add').addEventListener('click', addRow);
            [].slice.call(document.querySelectorAll('[name="ctg_id"]')).map((e) => {
                e.addEventListener('click', renderFields);
            });
        });

        const renderFields = (e) => {
            if (e.target.dataset.meta) {
                meta = JSON.parse(e.target.dataset.meta);
                quota = JSON.parse(e.target.dataset.quota);
                quota = quota < 0 ? 365 : quota;
                let time_input = meta && meta.time_input

                if (time_input) {
                    Array.from(document.querySelectorAll('.hide_if_start_only')).map((el) => {
                        el.classList.toggle('d-none', meta.time_input == 'start_only');
                    })
                }

                document.querySelector('#hide_if_date_only').classList.toggle('d-none', !(meta && meta.hasOwnProperty('time_input')));
                document.querySelector('[name="time_start"]').required = (time_input ? 'required' : '');
                document.querySelector('[name="time_end"]').required = (time_input == 'start_to_end' ? 'required' : '');

                Array.from(tbody.children).map((el, i) => { if (i > 0) el.remove(); })
                Array.from(document.querySelectorAll('.inputs-meta-fields')).map((el) => el.classList.add('d-none'));

                // Reset date values
                Array.from(document.querySelectorAll('[name="dates[]"]')).map((el) => el.value = '');
                toggleAddButtonBasedQuota();
            }
        }

        const toggleAddButtonBasedQuota = () => {
            const addBtn = document.getElementById('fields-options-add');
            const isAtQuota = tbody.children.length >= quota;
            addBtn.classList.toggle('disabled', isAtQuota);
            addBtn.style.pointerEvents = isAtQuota ? 'none' : 'auto';
            addBtn.style.opacity = isAtQuota ? '0.5' : '1';
        }

        const addRow = () => {
            let trTemplate = document.querySelector('#fields-options-template');
            if (tbody.children.length < quota) {
                let newRow = trTemplate.cloneNode(true);
                newRow.removeAttribute('id');
                newRow.querySelector('input').value = '';
                newRow.querySelector('.btn-delete').classList.remove('d-none');
                tbody.appendChild(newRow);
            }
            toggleAddButtonBasedQuota();
        }

        const removeRow = (e) => {
            const row = e.target.closest('.d-flex');
            if(row) row.remove();
            toggleAddButtonBasedQuota();
        }
    </script>
@endpush
