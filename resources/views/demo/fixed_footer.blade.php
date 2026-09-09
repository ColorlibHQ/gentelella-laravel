{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/fixed_footer.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Fixed footer')
@section('page_key', 'fixed-footer')
@section('breadcrumb', 'Home > Fixed footer')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Layouts</div>
        <h1 class="page-title">Fixed footer</h1>
      </div>
    </div>
  </div>

  <div class="row col-1">
    <div class="alert alert-info">
      <svg class="alert-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v.01M8 7v4"/></svg>
      <div class="alert-body"><strong>Page-scoped variant.</strong> This page pins the footer to the viewport bottom via a small CSS override. The default v4 footer scrolls with content.</div>
    </div>

    <div class="card"><div class="card-body" style="font-size:13px;color:var(--text-secondary);line-height:1.7;padding:24px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div></div>
    <div class="card"><div class="card-body" style="font-size:13px;color:var(--text-secondary);line-height:1.7;padding:24px">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div></div>
    <div class="card"><div class="card-body" style="font-size:13px;color:var(--text-secondary);line-height:1.7;padding:24px">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</div></div>
    <div class="card"><div class="card-body" style="font-size:13px;color:var(--text-secondary);line-height:1.7;padding:24px">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div></div>
  </div>
@endverbatim
@endsection
