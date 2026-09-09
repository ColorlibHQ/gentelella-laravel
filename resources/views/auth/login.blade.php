@extends('gentelella::layouts.blank')

@section('title', __('Sign in'))

@section('content')
<x-gentelella::auth.card :title="__('Welcome back')" :subtitle="__('Sign in to continue to your dashboard.')">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <div class="input-group">
                <svg class="input-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="2" y="3" width="12" height="10" rx="1.5"/><path d="M2 5l6 4 6-4"/></svg>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="you@company.com" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <svg class="input-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="7" width="10" height="7" rx="1.5"/><path d="M5 7V5a3 3 0 016 0v2"/></svg>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="••••••••" required autocomplete="current-password">
            </div>
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="auth-actions">
            <label class="form-check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('Remember me') }}
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
            {{ __('Sign in') }}
        </button>
    </form>

    @if (Route::has('register'))
        <x-slot:footer>
            {{ __("Don't have an account?") }} <a href="{{ route('register') }}">{{ __('Create one') }}</a>
        </x-slot>
    @endif
</x-gentelella::auth.card>
@endsection
