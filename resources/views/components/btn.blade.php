{{--
    Button, or a link styled as one when `href` is given.

    variant: primary | outline | ghost | danger | success | warning (default outline)
    size:    sm | lg
--}}
@props(['variant' => 'outline', 'size' => null, 'href' => null, 'type' => 'button', 'icon' => false])

@php
    $classes = ['btn', 'btn-'.$variant, $size ? 'btn-'.$size : null, $icon ? 'btn-icon' : null];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
