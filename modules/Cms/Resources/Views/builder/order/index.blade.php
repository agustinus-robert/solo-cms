@extends('cms::layouts.default')

@section('title', 'Order')

@section('navtitle', 'Order')

@section('content')
    @livewire('cms::builder.order')
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Nestable/2012-10-15/jquery.nestable.min.js"></script>
@endpush
