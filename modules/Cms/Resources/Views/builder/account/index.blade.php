@extends('cms::layouts.admin')

@extends('cms::layouts.components.navbar-admin')

@section('title', 'Account')

@section('navtitle', 'Account')

@section('content')
    <div class="toolbar mb-lg-7 mb-5" id="kt_toolbar">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column me-3">
            <!--begin::Title-->
            <h1 class="d-flex fw-bold fs-3 my-1 text-gray-900">Account</h1>
            <!--end::Title-->
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-dot fw-semibold fs-7 my-1 text-gray-600">
                <!--begin::Item-->
                <li class="breadcrumb-item text-gray-600">
                    <a href="index.html" class="text-hover-primary text-gray-600">Dashboard</a>
                </li>

                <li class="breadcrumb-item text-gray-600">Account</li>
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

        @livewire('cms::builder.account')
    </div>
@endsection
