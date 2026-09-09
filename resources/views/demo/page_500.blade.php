{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/page_500.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Server error')

@section('content')
@verbatim
<div class="error-page">
  <div class="error-content">
    <div class="error-code">500</div>
    <div class="error-title">Something went wrong</div>
    <div class="error-message">
      We hit an unexpected error on our end. Our team has been notified.
      Try refreshing the page in a moment, or head back to the dashboard.
    </div>
    <div class="error-actions">
      <a href="index.html" class="btn btn-primary">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 2L2 7v7h12V7L8 2z"/><path d="M6 14V9h4v5"/></svg>
        Back to dashboard
      </a>
      <a href="javascript:location.reload()" class="btn btn-outline">↻ Try again</a>
    </div>
  </div>
</div>
@endverbatim
@endsection
