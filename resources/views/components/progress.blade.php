{{--
    Thin progress bar. `value` is a percentage; it is clamped and cast before it
    reaches the inline style, so a value from user data cannot break out of it.
--}}
@props(['value' => 0, 'tone' => 'primary'])

@php
    $pct = max(0, min(100, (float) $value));
@endphp

<div {{ $attributes->merge(['class' => 'progress-thin']) }} role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
    <div class="bar" style="width:{{ $pct }}%;background:var(--{{ preg_replace('/[^a-z0-9-]/', '', $tone) }})"></div>
</div>
