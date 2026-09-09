{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/lock_screen.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::layouts.blank')

@section('title', 'Locked')

@section('content')
@verbatim
<div class="auth-page">
  <div class="auth-card lock-screen">
    <div class="lock-avatar" aria-hidden="true">
      <div class="lock-avatar-circle" style="background:linear-gradient(135deg,var(--primary),var(--primary-dk))">A</div>
      <div class="lock-icon">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="7" width="10" height="7" rx="1.5"/><path d="M5 7V5a3 3 0 016 0v2"/></svg>
      </div>
    </div>

    <div class="auth-title" style="text-align:center;margin-top:14px">Welcome back, Aigars</div>
    <div class="auth-subtitle" style="text-align:center">Your session was locked for security. Enter your password to continue.</div>

    <form onsubmit="event.preventDefault(); window.location.href='index.html';">
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
          <svg class="input-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="7" width="10" height="7" rx="1.5"/><path d="M5 7V5a3 3 0 016 0v2"/></svg>
          <input type="password" id="password" class="form-control" placeholder="••••••••" required autofocus>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
        Unlock
      </button>
    </form>

    <div class="auth-footer">
      Not Aigars? <a href="login.html">Sign in as someone else</a>
    </div>
  </div>
</div>
@endverbatim
@endsection
