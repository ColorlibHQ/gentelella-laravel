{{--
    Shared error page. Laravel resolves errors::<code>; each code extends this
    with its own wording.
--}}
@extends('gentelella::layouts.blank')

@section('title', $title)

@section('content')
<div class="error-page">
    <div class="error-content">
        <div class="error-code">{{ $code }}</div>
        <div class="error-title">{{ $title }}</div>
        <div class="error-message">{{ $message }}</div>
        <div class="error-actions">
            <a href="{{ $home }}" class="btn btn-primary">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M8 2L2 7v7h12V7L8 2z"/><path d="M6 14V9h4v5"/></svg>
                {{ __('Back to dashboard') }}
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">&larr; {{ __('Go back') }}</a>
        </div>
    </div>
</div>
@endsection
