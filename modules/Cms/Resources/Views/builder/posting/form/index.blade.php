@extends('cms::layouts.admin')

@extends('cms::layouts.components.navbar-admin')

@section('title', 'Posting Form')

@section('navtitle', 'Posting Form')

@section('content')

    @livewire('cms::builder.form')

@endsection
