{{--
    Avatar. Renders an <img> when `src` is set, otherwise the first letter of
    `name`. size: xs | sm | md | lg | xl | xxl. `status` adds the presence dot.
--}}
@props(['name' => null, 'src' => null, 'size' => null, 'status' => null])
@php
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) ($name ?? $slot)), 0, 1));
@endphp
<div {{ $attributes->class(['avatar', $size ? 'avatar-'.$size : null]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name ?? '' }}">
    @else
        {{ $initial }}
    @endif
    @if ($status)
        <span class="{{ $status === 'online' ? 'online' : 'avatar-status '.$status }}"></span>
    @endif
</div>
