{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/page_403.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Forbidden')

@section('content')
@verbatim
<div class="error-page">
  <div class="error-content">
    <div class="error-code">403</div>
    <div class="error-title">Access denied</div>
    <div class="error-message">
      You don't have permission to view this page. If you think this is a
      mistake, please reach out to your account administrator.
    </div>
    <div class="error-actions">
      <a href="index.html" class="btn btn-primary">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 2L2 7v7h12V7L8 2z"/><path d="M6 14V9h4v5"/></svg>
        Back to dashboard
      </a>
      <a href="login.html" class="btn btn-outline">Sign in as another user</a>
    </div>
  </div>
</div>
@endverbatim
@endsection
