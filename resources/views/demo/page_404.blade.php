{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/page_404.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Page not found')

@section('content')
@verbatim
<div class="error-page">
  <div class="error-content">
    <div class="error-code">404</div>
    <div class="error-title">Page not found</div>
    <div class="error-message">
      The page you're looking for doesn't exist or has been moved.
      Try heading back to the dashboard or use the search to find what you need.
    </div>
    <div class="error-actions">
      <a href="index.html" class="btn btn-primary">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 2L2 7v7h12V7L8 2z"/><path d="M6 14V9h4v5"/></svg>
        Back to dashboard
      </a>
      <a href="javascript:history.back()" class="btn btn-outline">← Go back</a>
    </div>
  </div>
</div>
@endverbatim
@endsection
