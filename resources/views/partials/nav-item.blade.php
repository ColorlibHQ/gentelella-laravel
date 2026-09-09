{{--
    One sidebar entry. Mirrors renderNavItem() in src/v4/shell-render.js:
    a parent renders a .nav-tree with a toggle button and a .nav-sub panel,
    a leaf renders a plain .nav-link. Icons are package-controlled SVG strings,
    so they are echoed raw; every text value is escaped.
--}}
@if (! empty($item['children']))
    <div @class(['nav-tree', 'open has-active' => $item['open'] ?? false])>
        <button type="button" class="nav-link nav-toggle" aria-expanded="{{ ($item['open'] ?? false) ? 'true' : 'false' }}">
            {!! $gentelella->icon($item['icon'] ?? null) !!}
            <span class="nav-text">{{ $item['text'] }}</span>
            @include('gentelella::partials.nav-badge', ['badge' => $item['badge'] ?? null])
            <svg class="nav-chev" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 4l4 4-4 4"/></svg>
        </button>
        <div class="nav-sub"><div class="nav-sub-inner">
            @foreach ($item['children'] as $child)
                <a @class(['nav-sublink', 'active' => $child['active'] ?? false])
                   href="{{ $child['href'] ?? '#' }}"
                   @if ($child['active'] ?? false) aria-current="page" @endif>{{ $child['text'] }}@include('gentelella::partials.nav-badge', ['badge' => $child['badge'] ?? null])</a>
            @endforeach
        </div></div>
    </div>
@else
    <a @class(['nav-link', 'active' => $item['active'] ?? false])
       href="{{ $item['href'] ?? '#' }}"
       @if ($item['active'] ?? false) aria-current="page" @endif>
        {!! $gentelella->icon($item['icon'] ?? null) !!}
        <span class="nav-text">{{ $item['text'] }}</span>
        @include('gentelella::partials.nav-badge', ['badge' => $item['badge'] ?? null])
    </a>
@endif
