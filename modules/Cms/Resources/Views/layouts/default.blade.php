@extends('layouts.vertical')

@include('cms::layouts.components.extra')

@section('titleTemplate', 'CMS Dashboard')

@section('bodyclass', 'bg-light')

@section('body-content')

<header id="page-topbar">
    <div class="navbar-header">

        <div class="d-flex">

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="/" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('skote/images/logo.svg') }}" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('skote/images/logo-dark.png') }}" height="17">
                    </span>
                </a>

                <a href="/" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('skote/images/logo-light.svg') }}" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('skote/images/logo-light.png') }}" height="39">
                    </span>
                </a>
            </div>

            <button type="button"
                class="btn btn-sm font-size-16 header-item waves-effect px-3"
                id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button"
                    class="btn header-item noti-icon waves-effect"
                    data-bs-toggle="dropdown">
                    <i class="mdi mdi-magnify"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- NAVBAR CMS --}}
            @include('cms::layouts.components.navbar')

        </div>

    </div>
</header>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">

            {{-- SIDEBAR CMS --}}
            @include('cms::layouts.components.sidebar')

        </div>
    </div>
</div>

<!-- Main Container -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <main class="animate__animated animate__fadeIn animate__faster">

                <x-alert-success />
                <x-alert-danger />

                @yield('content')

            </main>

        </div>
    </div>

</div>
<!-- END Main Container -->

<footer class="footer">
    <div class="container-fluid">
        <div class="row">

            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> © CMS
            </div>

            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    CMS System
                </div>
            </div>

        </div>
    </div>
</footer>

@endsection
