{{--
    The admin shell. Extend this from any page:

        @extends('gentelella::page')
        @section('title', 'Dashboard')
        @section('page_key', 'dashboard')
        @section('breadcrumb', 'Home > Dashboard')
        @section('content') ... @endsection

    A controller may pass $pageKey / $breadcrumb instead of declaring sections;
    the variables win. DOM order matches the static template exactly: skip link,
    sidebar, topbar, then <main> with the footer inside it.
--}}
@php
    // Blade runs inline @section values through e(), so "Home > Forms" arrives
    // as "Home &gt; Forms" and would never split on the separator. Decode before
    // parsing; every crumb's text is re-escaped on output.
    $pageKey = trim($pageKey ?? $__env->yieldContent('page_key'));
    $breadcrumbLine = trim($breadcrumb ?? html_entity_decode($__env->yieldContent('breadcrumb'), ENT_QUOTES)) ?: 'Home';
    $shell = true;
    $menu = $gentelella->menu($pageKey);
    $crumbs = $gentelella->breadcrumb($breadcrumbLine, $pageKey);
@endphp

@extends('gentelella::layouts.base')

@push('head')
{{-- Hands the design system this app's menu and URLs. Without it the Cmd+K
     palette lists the static template's demo pages and every result 404s. --}}
<script type="application/json" id="gentelella-shell-config">@json($gentelella->shellConfig($pageKey))</script>
@endpush

@section('body')
<a class="skip-link" href="#main-content">{{ __('Skip to main content') }}</a>

@include('gentelella::partials.sidebar', ['menu' => $menu, 'gentelella' => $gentelella])
@include('gentelella::partials.topbar', ['crumbs' => $crumbs])

<main id="main-content" tabindex="-1" class="main">
<div class="page-wrapper">
@yield('content')
</div>
@include('gentelella::partials.footer')
</main>
@endsection
