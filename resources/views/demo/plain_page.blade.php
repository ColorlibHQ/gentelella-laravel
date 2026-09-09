{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/plain_page.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Blank page')
@section('page_key', 'plain')
@section('breadcrumb', 'Home > Blank page')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Template</div>
        <h1 class="page-title">Blank page</h1>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:40px;text-align:center;color:var(--text-muted)">
      <div style="font-size:14px;margin-bottom:6px;color:var(--text)">This page is a starter template.</div>
      <div style="font-size:12.5px">Drop your content into the <code>.card</code> below or replace this whole structure.</div>
    </div>
  </div>
@endverbatim
@endsection
