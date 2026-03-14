@extends('cms::layouts.admin')

@extends('cms::layouts.components.navbar-admin')

@section('title', 'Site Config')

@section('navtitle', 'Site Config')

@section('content')

    @livewire('cms::configure.sites-config')

@endsection
