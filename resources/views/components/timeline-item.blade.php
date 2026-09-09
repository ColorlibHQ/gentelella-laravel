{{-- tone drives the marker colour: primary | green | yellow | red | blue --}}
@props(['title', 'time' => null, 'tone' => 'primary', 'description' => null])

<div {{ $attributes->class(['timeline-item', 'is-'.$tone]) }}>
    @if ($time)<div class="ti-time">{{ $time }}</div>@endif
    <div class="ti-title">{{ $title }}</div>
    @if ($description)<div class="ti-desc">{{ $description }}</div>@endif
    {{ $slot }}
</div>
