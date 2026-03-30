@extends('finance::layouts.default')

@section('title', 'Template PPh21 | ')
@section('navtitle', 'Template PPh21')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <section>
                <div class="card border-0">
                    <div class="card-body">
                        <i class="mdi mdi-format-list-bulleted"></i> Template PPh21
                    </div>
                    <div class="card-body border-top border-light">
                        <form class="form-block row g-2" action="{{ route('finance::tax.templates.index') }}" method="get">
                            <input name="trash" type="hidden" value="{{ request('trash') }}">
                            <div class="flex-grow-1 col-auto">
                                <input class="form-control" name="search" placeholder="Cari nama ..." value="{{ request('search') }}" />
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-light" href="{{ route('finance::tax.templates.index', request()->only('trashed', 'closed')) }}"><i class="mdi mdi-refresh"></i> <span class="d-sm-none">Reset</span></a>
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
                                        <a class="btn btn-soft-warning rounded px-2 py-1" href="{{ route('finance::tax.templates.show', ['template' => $template->id, 'next' => url()->current()]) }}" method="post" data-bs-toggle="tooltip" title="Ubah"><i class="mdi mdi-pencil-outline"></i></a>
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
            <div class="card border-0">
                <div class="card-body">Menu lainnya</div>
                <div class="list-group list-group-flush border-top border-light">
                    <a class="list-group-item list-group-item-action text-dark" href="{{ route('finance::tax.templates.create') }}"><i class="mdi mdi-plus"></i> Tambah template</a>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-body"><i class="mdi mdi-account"></i> Objek pajak</div>
                <div class="card-body border-top">
                    <form action="{{ route('finance::tax.templates.config') }}" method="post" class="form-block" enctype="multipart/form-data">@csrf
                        @foreach ($objects as $key => $object)
                            <div class="mb-3">
                                <label for="{{ $object->key() }}" class="col-form-label required">{{ $object->label() }}</label>
                                <input type="text" class="form-control @error('rate') is-invalid @enderror" name="rate[{{ $object->key() }}]" id="{{ $object->key() }}" value="{{ $configs[$object->key()]['rate'] ?? 0 }}">
                            </div>
                        @endforeach
                        <div class="mb-3">
                            <button class="btn btn-soft-danger"><i class="mdi mdi-content-save-outline"></i> Simpan</button>
                            <a href="{{ route('finance::tax.templates.index') }}" class="btn btn-soft-secondary"><i class="mdi mdi-sync"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
