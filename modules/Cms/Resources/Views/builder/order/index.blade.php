@extends('cms::layouts.default')

@extends('cms::layouts.components.navbar-admin')

@section('title', 'Order')

@section('navtitle', 'Order')

@section('content')
    @livewire('cms::builder.order')
@endsection
