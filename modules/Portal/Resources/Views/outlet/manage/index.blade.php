@extends('portal::layouts.index')

@section('title', env('APP_NAME') . ' Outlet')

@section('navtitle', env('APP_NAME') . ' Outlet')

@section('contents')
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
    </header>

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-3 mt-6">
                    <h2 class="fw-light mb-0">Outlet</h2>

                    @if (str_contains(url()->full(), 'create') == false || !str_contains(url()->full(), 'edit' == false))
                        <a href="{{ route('portal::outlet.manage-outlet.create') }}" class="btn btn-primary btn-sm btn-primary rounded-pill px-3">
                            <i class="fa fa-plus me-1 opacity-50"></i> Buat Outlet
                        </a>
                    @endif
                </div>


                @if (Session::has('msg-sukses'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 1500)" x-show="show">
                        <div class="alert alert-success">
                            {{ Session::get('msg-sukses') }}
                        </div>
                    </div>
                @endif

                @if (Session::has('msg-gagal'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 1500)" x-show="show">
                        <div class="alert-danger alert">
                            {{ Session::get('msg-gagal') }}
                        </div>
                    </div>
                @endif

                @if (str_contains(url()->full(), 'create') || str_contains(url()->full(), 'edit'))
                    @livewire('poz::master.outlet', ['action' => $action])
                @else
                    @php
                        $arr = [
                            'global' => false,
                            'column' => $column,
                            'ajax' => [
                                'url' => route('portal::outlet.outlet.datatables'),
                                'script' => 'function(d) { ajaxDataFunction(d); }',
                            ],
                            'parameters' => [
                                'drawCallback' => 'function() { ajaxParam(); }',
                            ],
                            'title' => 'Daftar Outlet',
                            'menu' => 'outlet',
                        ];
                    @endphp

                    <div class="block-content block-content-full"> <!--begin::Header-->
                        <div class="row">
                            @livewire('poz::datatables.custom-datatable', ['arr' => $arr])
                        </div> <!--end::Body-->
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<script>
    function ajaxDataFunction(d) {
        d.filterTitle = $("#kt_filter_search").val(); // Sesuaikan parameter yang ingin ditambahkan
        d.filterInstansi = $('#filter_instansi').val();
        d.filterJobs = $('#filter_jobs').val();
    }

    function ajaxParam() {

    }
</script>
