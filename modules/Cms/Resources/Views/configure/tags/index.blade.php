@extends('cms::layouts.admin')

@extends('cms::layouts.components.navbar-admin')

@section('title', 'Tags')

@section('navtitle', 'Tags')

@section('content')

    @if (str_contains(url()->full(), 'create') || str_contains(url()->full(), 'edit'))
        @livewire('cms::configure.tags')
    @else
        <div class="toolbar mb-lg-7 mb-5" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column me-3">
                <!--begin::Title-->
                <h1 class="d-flex fw-bold fs-3 my-1 text-gray-900">Tags</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-dot fw-semibold fs-7 my-1 text-gray-600">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-600">
                        <a href="index.html" class="text-hover-primary text-gray-600">Dashboard</a>
                    </li>

                    <li class="breadcrumb-item text-gray-600">Tags</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>

        <div class="content flex-column-fluid" id="kt_content">

            @if (Session::has('msg'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 1500)" x-show="show">
                    <div class="alert alert-success">
                        {{ Session::get('msg') }}
                    </div>
                </div>
            @endif

            @if (Session::has('msg-server'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 1500)" x-show="show">
                    <div class="alert alert-danger">
                        {{ Session::get('msg') }}
                    </div>
                </div>
            @endif


            <div class="row">
                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2 shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-info text-uppercase mb-1 text-xs">
                                        Total Post Using this Tags</div>
                                    <div class="h5 font-weight-bold mb-0 text-gray-800">18</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2 shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-info text-uppercase mb-1 text-xs">
                                        Total Post By List Tags
                                        <select name="day" style="border:1px solid white;">
                                            <option value="">Choose List Category</option>
                                        </select>
                                    </div>
                                    <div class="h5 font-weight-bold mb-0 text-gray-800">
                                        <div id="html_role">-</div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow">
                <div class="card-header align-items-center gap-md-5 gap-2 py-5">
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            {{-- <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i> --}}
                            List of Tags Data
                        </div>
                    </div>

                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <!--begin::Add product-->
                        <a href="{{ route('cms::configure.tags.create') }}" class="btn btn-primary"> Add Tags</a>
                        <!--end::Add product-->
                    </div>

                </div>

                <div class="card-body">
                    @livewire('cms::datatables.tags-datatable')
                </div>
            </div>
        </div>
    @endif

@endsection
