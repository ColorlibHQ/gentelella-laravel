{{-- Mirrors renderSidebar() in src/v4/shell-render.js. --}}
@php
    $user = auth()->user();
    $userName = $user?->{config('gentelella.user.name_field', 'name')} ?? config('gentelella.user.fallback_name');
    $userRole = $user?->{config('gentelella.user.role_field', 'role')} ?? config('gentelella.user.fallback_role');
@endphp
<aside class="sidebar" aria-label="{{ __('Primary navigation') }}">
    <div class="sidebar-brand">
        <div class="brand-icon">{{ config('gentelella.brand_initial') }}</div>
        <div class="brand-name">{{ config('gentelella.brand_name') }} <small>{{ config('gentelella.brand_suffix') }}</small></div>
    </div>

    <nav class="sidebar-nav">
        @foreach ($menu->groups as $group)
            <div class="nav-group">
                <div class="nav-label">{{ $group['label'] }}</div>
                @foreach ($group['items'] as $item)
                    @include('gentelella::partials.nav-item', ['item' => $item])
                @endforeach
            </div>
        @endforeach
    </nav>

    @if (config('gentelella.user.enabled', true))
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) $userName, 0, 1)) }}<span class="online"></span></div>
                <div class="sidebar-user-info">
                    <div class="name">{{ $userName }}</div>
                    <div class="role">{{ $userRole }}</div>
                </div>
                <button class="more-btn" aria-label="{{ __('More options') }}">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><circle cx="8" cy="3" r="1.2"/><circle cx="8" cy="8" r="1.2"/><circle cx="8" cy="13" r="1.2"/></svg>
                </button>
            </div>
        </div>
    @endif
</aside>
