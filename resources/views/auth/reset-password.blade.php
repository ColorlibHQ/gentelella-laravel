@extends('gentelella::layouts.blank')

@section('title', __('Choose a new password'))

@section('content')
<x-gentelella::auth.card :title="__('Choose a new password')">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="{{ old('email', $email ?? '') }}" required autocomplete="username">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">{{ __('New password') }}</label>
            <input type="password" id="password" name="password" class="form-control"
                   required autofocus autocomplete="new-password">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
            {{ __('Reset password') }}
        </button>
    </form>
</x-gentelella::auth.card>
@endsection
