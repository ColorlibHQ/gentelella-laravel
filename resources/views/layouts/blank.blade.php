{{--
    Bare page — no sidebar, no topbar. For auth screens, error pages and the
    landing page, which the static template ships without data-shell="admin".
--}}
@extends('gentelella::layouts.base')

@section('body')
@yield('content')
@endsection
