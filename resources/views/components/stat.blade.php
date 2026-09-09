{{--
    Dashboard stat card.

    tone     colours the icon square (teal, blue, purple, orange, red, …)
    change   signed percentage; direction is inferred unless `direction` is set
    spark    list of 0-100 bar heights for the mini sparkline
--}}
@props([
    'label',
    'value',
    'tone' => 'teal',
    'change' => null,
    'direction' => null,
    'subtext' => null,
    'spark' => [],
])

@php
    $dir = $direction ?? (str_starts_with(trim((string) $change), '-') ? 'down' : 'up');
@endphp

<div {{ $attributes->merge(['class' => 'stat']) }}>
    @isset($icon)
        <div class="stat-icon {{ $tone }}">{{ $icon }}</div>
    @endisset

    <div class="stat-content">
        <div class="stat-label">{{ $label }}</div>
        <div class="stat-value-row">
            <span class="stat-value">{{ $value }}</span>
            @if ($change !== null)
                <span class="stat-change {{ $dir }}">
                    <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="{{ $dir === 'up' ? 'M6 9V3M3 6l3-3 3 3' : 'M6 3v6M3 6l3 3 3-3' }}"/>
                    </svg>{{ $change }}
                </span>
            @endif
        </div>
        @if ($subtext)<div class="stat-subtext">{{ $subtext }}</div>@endif
    </div>

    @if ($spark !== [])
        <div class="stat-spark">@foreach ($spark as $h)<div class="bar" style="height:{{ max(0, min(100, (float) $h)) }}%"></div>@endforeach</div>
    @endif
</div>
