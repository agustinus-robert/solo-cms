@extends('cms::layouts.default')

@section('content')
    @if (str_contains(url()->full(), 'create') || str_contains(url()->full(), 'edit'))
        @livewire('cms::builder.menu')
    @else
        <div class="row">
            <div class="col-md-12">
                <section>
                    <div class="card border-0">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="mdi mdi-format-list-bulleted"></i>
                                    <span>Daftar Menu</span>
                                </div>
                                <a class="btn btn-primary" href="{{ route('cms::builder.menu.create') }}">Tambah</a>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            @livewire('cms::datatables.menu-datatable')
                        </div>
                </section>
            </div>
        </div>
    @endif
@endsection
