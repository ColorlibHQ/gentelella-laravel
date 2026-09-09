@extends('gentelella::layouts.blank')

@section('title', __('Create account'))

@section('content')
<x-gentelella::auth.card :title="__('Create your account')" :subtitle="__('It takes less than a minute.')">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}"
                   required autofocus autocomplete="name">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                   placeholder="you@company.com" required autocomplete="username">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" class="form-control"
                   required autocomplete="new-password">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
            {{ __('Create account') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </x-slot>
</x-gentelella::auth.card>
@endsection
