{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/inbox.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Inbox')
@section('page_key', 'inbox')
@section('breadcrumb', 'Home > Inbox')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Communication</div>
        <h1 class="page-title">Inbox</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline" type="button">Mark all read</button>
        <button class="btn btn-primary" type="button">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 14L14 2M14 2H6M14 2v8"/></svg>
          Compose
        </button>
      </div>
    </div>
  </div>

  <div class="card">
    <div id="inbox-root"></div>
  </div>
@endverbatim
@endsection
