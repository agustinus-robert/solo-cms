@extends('web::electro.index')

@section('title', "Beranda")

@section('content')
    @foreach($sections as $section)
        @includeIf($section['view'], $section['data'])
    @endforeach
@endsection
