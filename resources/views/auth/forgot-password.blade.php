@extends('gentelella::layouts.blank')

@section('title', __('Reset password'))

@section('content')
<x-gentelella::auth.card
    :title="__('Forgot your password?')"
    :subtitle="__('Enter your email and we will send you a reset link.')">

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <div class="input-group">
                <svg class="input-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="2" y="3" width="12" height="10" rx="1.5"/><path d="M2 5l6 4 6-4"/></svg>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="you@company.com" required autofocus autocomplete="username">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
            {{ __('Send reset link') }}
        </button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
    </x-slot>
</x-gentelella::auth.card>
@endsection
