@extends('hrms::layouts.default')

@section('title', 'Template Upah BPJS | ')

@section('navtitle', 'Template Upah BPJS')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <section>
                <div class="card border-0">
                    <div class="card-body">
                        <i class="mdi mdi-format-list-bulleted"></i> Template Upah BPJS
                    </div>
                    <div class="card-body border-top border-light">
                        <form class="form-block row g-2" action="{{ url()->full() }}" method="get">
                            <input name="trash" type="hidden" value="{{ request('trash') }}">
                            <div class="flex-grow-1 col-auto">
                                <input class="form-control" name="search" placeholder="Cari nama ..." value="{{ request('search') }}" />
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-light" href="{{ route('hrms::benefit.insurances.templates.index', request()->only('trashed')) }}"><i class="mdi mdi-refresh"></i> <span class="d-sm-none">Reset</span></a>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-dark"><i class="mdi mdi-magnify"></i> Cari</button>
                            </div>
                        </form>
                    </div>
                    <div class="list-group list-group-flush border-top">
                        @forelse($templates as $template)
                            <div class="list-group-item border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="col-auto">
                                                <div class="bg-light me-3 rounded px-3 py-2">#{{ $loop->iteration + $templates->firstItem() - 1 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                {{ $template->key }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <a class="btn btn-soft-warning rounded px-2 py-1" href="{{ route('hrms::benefit.insurances.templates.show', ['template' => $template->id, 'next' => url()->current()]) }}" method="post" data-bs-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil-outline"></i></a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item">
                                @include('components.notfound')
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <div class="card card-body d-flex justify-content-between align-items-center flex-row border-0 py-4">
                <div>
                    <div class="display-4">{{ $template_count }}</div>
                    <div class="small fw-bold text-secondary text-uppercase">Jumlah template PPh21</div>
                </div>
                <div><i class="mdi mdi-file-tree-outline mdi-48px text-light"></i></div>
            </div>
            <div class="card mb-4 border-0">
                <div class="card-body"><i class="mdi mdi-cog"></i> Menu lainnya</div>
                <div class="list-group list-group-flush border-top border-light">
                    <a class="list-group-item list-group-item-action text-dark" href="{{ route('hrms::benefit.insurances.templates.create', ['next' => url()->current()]) }}"> Tambah template</a>
                </div>
            </div>
            <div class="card mb-3 border-0">
                <div class="card-body"><i class="mdi mdi-cog"></i> Setting</div>
                <div class="card-body border-top">
                    <form class="form-block form-confirm" action="{{ route('hrms::benefit.insurances.templates.default-salary', ['next' => url()->full()]) }}" method="POST" enctype="multipart/form-data">@csrf
                        <div class="mb-4">
                            <label for="max" class="form-label required">Maksimal gaji di BPSJ Kesehatan</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input class="form-control text-start" type="number" min="0" step="0.01" name="max_salary" id="max_salary" value="{{ bpjs_max_salary() }}">
                                </div>
                                @error('max_salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="max" class="form-label required">Minimal gaji di BPSJ Kesehatan</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input class="form-control text-start" type="number" min="0" step="0.01" name="min_salary" id="min_salary" value="{{ bpjs_min_salary() }}">
                                </div>
                                @error('min_salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="max" class="form-label required">BPSJ Kesehatan limit kelas</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input class="form-control text-start" type="number" min="0" step="0.01" name="limit_salary" id="limit_salary" value="{{ bpjs_kes_limit() }}">
                                </div>
                                @error('limit_salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr class="border">
                        <div class="mb-4">
                            <label for="max" class="form-label required">Maksimal gaji di BPSJ Jaminan Pensiun</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input class="form-control text-start" type="number" min="0" step="0.01" name="max_tk_pensiun_salary" id="max_tk_pensiun" value="{{ bpjs_tk_pensiun_max_salary() }}">
                                </div>
                                @error('max_tk_pensiun_salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="max" class="form-label required">Minimal gaji di BPSJ Ketenagakerjaan</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input class="form-control text-start" type="number" min="0" step="0.01" name="min_tk_salary" id="min_tk_salary" value="{{ bpjs_tk_min_salary() }}">
                                </div>
                                @error('min_tk_salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush
