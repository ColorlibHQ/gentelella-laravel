{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/offline.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Offline')

@section('content')
@verbatim
<main class="auth-shell">
  <div class="auth-card" style="text-align:center;max-width:420px">
    <div style="width:72px;height:72px;border-radius:50%;background:var(--bg-surface-secondary);color:var(--text-muted);display:flex;align-items:center;justify-content:center;margin:0 auto 18px">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M5 13a10 10 0 0114 0M2 9a14 14 0 0120 0M9 17a4 4 0 016 0"/>
        <line x1="3" y1="3" x2="21" y2="21"/>
      </svg>
    </div>
    <h1 class="auth-title">You're offline</h1>
    <p class="auth-subtitle">This page hasn't been cached yet. Reconnect and try again — we'll be right here.</p>
    <button type="button" class="btn btn-primary" style="width:100%;justify-content:center;height:38px" onclick="location.reload()">Try again</button>
  </div>
</main>
@endverbatim
@endsection
