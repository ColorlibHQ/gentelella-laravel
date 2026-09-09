{{-- Renders an <a> when href is set, otherwise a button. `meta` is the right-aligned value. --}}
@props(['href' => null, 'active' => false, 'meta' => null])

@php $classes = ['list-group-item', 'active' => $active]; @endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}@if ($meta !== null)<span class="meta">{{ $meta }}</span>@endif</a>
@else
    <button type="button" {{ $attributes->class($classes) }}>{{ $slot }}@if ($meta !== null)<span class="meta">{{ $meta }}</span>@endif</button>
@endif
