@extends('web::electro.index')

@section('title', "Shop")

@section('content')
    @foreach($sections as $section)
        @includeIf($section['view'], $section['data'])
    @endforeach
@endsection
