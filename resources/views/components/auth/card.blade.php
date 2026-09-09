{{--
    The shared auth card: brand, heading, and whatever the screen puts inside.
    Mirrors .auth-page / .auth-card from the static template.
--}}
@props(['title', 'subtitle' => null])
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="brand-icon">{{ config('gentelella.brand_initial') }}</div>
            <div class="brand-name">{{ config('gentelella.brand_name') }}
                <small style="font-weight:400;color:var(--text-muted);font-size:13px;margin-left:2px">{{ config('gentelella.brand_suffix') }}</small>
            </div>
        </div>

        <div class="auth-title">{{ $title }}</div>
        @if ($subtitle)<div class="auth-subtitle">{{ $subtitle }}</div>@endif

        @if (session('status'))
            <div class="auth-success">{{ session('status') }}</div>
        @endif

        @error('email')
            <div class="form-error" role="alert">{{ $message }}</div>
        @enderror

        {{ $slot }}

        @isset($footer)<div class="auth-footer">{{ $footer }}</div>@endisset
    </div>
</div>
