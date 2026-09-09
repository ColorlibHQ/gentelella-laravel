{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/maintenance.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Under maintenance')

@section('content')
@verbatim
<div class="error-page">
  <div class="error-content" style="max-width:560px">
    <div class="status-page-icon">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33h.07a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82v.07a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
      </svg>
    </div>
    <div class="error-title">We'll be right back</div>
    <div class="error-message">
      Gentelella is undergoing scheduled maintenance to make things faster and more reliable.
      We expect to be back online shortly. Thanks for your patience.
    </div>
    <div class="status-page-meta">
      <div class="meta-item">
        <div class="meta-label">Started</div>
        <div class="meta-value">Apr 28, 2026 · 02:00 UTC</div>
      </div>
      <div class="meta-item">
        <div class="meta-label">Estimated completion</div>
        <div class="meta-value">Apr 28, 2026 · 04:00 UTC</div>
      </div>
    </div>
    <div class="error-actions">
      <a href="https://twitter.com" class="btn btn-outline" rel="noopener">Status updates</a>
      <a href="mailto:support@example.com" class="btn btn-outline">Contact support</a>
    </div>
  </div>
</div>
@endverbatim
@endsection
