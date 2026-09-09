{{-- Mirrors renderTopbar() in src/v4/shell-render.js. --}}
<header class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" type="button" aria-label="{{ __('Open menu') }}" aria-controls="sidebar" aria-expanded="false">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
            @foreach ($crumbs as $crumb)
                @if (! $loop->first)<span class="sep" aria-hidden="true">›</span>@endif
                @if ($crumb['href'])
                    <a href="{{ $crumb['href'] }}">{{ $crumb['text'] }}</a>
                @else
                    <span @if ($crumb['current']) class="current" aria-current="page" @endif>{{ $crumb['text'] }}</span>
                @endif
            @endforeach
        </nav>
    </div>

    @if (config('gentelella.search_enabled', true))
        <div class="search-box">
            <svg class="s-icon" width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="7" cy="7" r="5"/><path d="M11 11l3.5 3.5"/></svg>
            <input type="text" placeholder="{{ __('Search pages or run a command…') }}" aria-label="{{ __('Open command palette') }}">
            <kbd>⌘K</kbd>
        </div>
    @endif

    <div class="topbar-right">
        @if ($docsUrl = config('gentelella.docs_url'))
            <a class="tb-btn tb-docs" href="{{ $docsUrl }}" target="_blank" rel="noopener" title="{{ __('Documentation') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 4h11a4 4 0 014 4v13H8a4 4 0 01-4-4V4z"/><path d="M4 17a4 4 0 014-4h11"/></svg>
                <span>{{ __('Docs') }}</span>
            </a>
        @endif

        @if (config('gentelella.theme_toggle', true))
            <button class="tb-btn theme-toggle" type="button" title="{{ __('Toggle theme') }}" aria-label="{{ __('Toggle theme') }}" aria-pressed="false">
                <svg class="theme-icon-light" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg class="theme-icon-dark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        @endif

        @if (config('gentelella.notifications_enabled', true))
            <button class="tb-btn tb-notifications" type="button" title="{{ __('Notifications') }}" aria-label="{{ __('Notifications') }}" aria-haspopup="dialog" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 3a6 6 0 00-6 6c0 6-3 7-3 7h18s-3-1-3-7a6 6 0 00-6-6z"/><path d="M10.5 21a1.5 1.5 0 003 0"/></svg>
                <span class="dot"></span>
            </button>
        @endif

        @if (config('gentelella.messages_enabled', true))
            <button class="tb-btn tb-messages" type="button" title="{{ __('Messages') }}" aria-label="{{ __('Messages') }}" aria-haspopup="dialog" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="M2 7l10 6 10-6"/></svg>
            </button>
        @endif

        <button class="tb-avatar" type="button" aria-label="{{ __('Account menu') }}" aria-haspopup="menu" aria-expanded="false">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) (auth()->user()?->name ?? config('gentelella.user.fallback_name')), 0, 1)) }}</button>
    </div>
</header>
