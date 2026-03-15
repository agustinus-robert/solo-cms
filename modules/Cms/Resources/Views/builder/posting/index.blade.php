@extends('cms::layouts.default')

@section('title', 'Posting')
@section('navtitle', 'Posting')

@section('content')

    @if (str_contains(url()->full(), 'create') || str_contains(url()->full(), 'edit'))
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-0">
                @livewire('cms::builder.posting')
            </div>
        </div>
    @else
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-8">
            <div>
                <h1 class="fw-bolder text-dark fs-2 mb-1">
                    {{ $type == 7 ? 'Form Management' : 'Posting Data' }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dot fw-semibold fs-7 p-0 m-0">
                        <li class="breadcrumb-item text-muted">Dashboard</li>
                        <li class="breadcrumb-item text-primary active">Posting</li>
                    </ol>
                </nav>
            </div>

            <div class="mt-4 mt-md-0">
                @if ($type == 7 || $create_status->add == 1)
                    <a href="{{ $type == 7 ? route('cms::builder.posting_form.create') : route('cms::builder.posting.create') }}?id_menu={{ $id_menu }}"
                       class="btn btn-dark btn-sm px-6 shadow-sm"
                       style="border-radius: 8px; transition: all 0.2s;">
                        <i class="ki-duotone ki-plus fs-4 me-1"></i>
                        Add New {{ $type == 7 ? 'Form' : 'Post' }}
                    </a>
                @endif
            </div>
        </div>

        @foreach (['msg' => 'success', 'msg-gagal' => 'danger', 'msg-server' => 'danger'] as $key => $type_alert)
            @if (Session::has($key))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                     x-transition.duration.500ms
                     class="alert alert-dismissible bg-light-{{ $type_alert }} border-{{ $type_alert }} border-dashed d-flex flex-column flex-sm-row p-4 mb-6">
                    <div class="d-flex flex-column pe-0 pe-sm-10 text-{{ $type_alert }}">
                        <span class="fw-bold text-dark fs-6">{{ Session::get($key) }}</span>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="card border-0 shadow-sm mb-5 mt-4" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header border-0 pt-8 bg-transparent">
                <div class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-gray-800">All Records</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Manage and organize your content efficiently</span>
                </div>
            </div>

            <div class="card-body pt-2 pb-8">
                <div class="table-responsive custom-datatable">
                    @livewire($type != 7 ? 'cms::datatables.posting-datatable' : 'cms::datatables.posting-form-datatable')
                </div>
            </div>
        </div>

        <style>
            /* Custom Subtle Styling */
            .card {
                background-color: #ffffff;
                box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.03) !important;
            }
            .breadcrumb-item.active { color: #0095E8 !important; }
            .btn-dark { background-color: #181c32 !important; border: none; }
            .btn-dark:hover { background-color: #000 !important; transform: translateY(-1px); }

            /* Make Datatable Clean */
            .custom-datatable .table thead th {
                background-color: #f9fafb;
                text-transform: uppercase;
                font-size: 0.75rem !important;
                letter-spacing: 0.05em;
                font-weight: 700;
                border-bottom: 1px solid #f1f1f4 !important;
                padding: 1rem !important;
            }
            .custom-datatable .table tbody td {
                padding: 1.25rem 1rem !important;
                vertical-align: middle;
            }
        </style>
    @endif

@endsection
