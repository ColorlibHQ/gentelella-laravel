{{-- Empty state. `icon` and the default slot (actions) are optional. --}}
@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    @isset($icon)<div class="empty-state-icon">{{ $icon }}</div>@endisset
    <div class="empty-state-title">{{ $title }}</div>
    @if ($description)<div class="empty-state-desc">{{ $description }}</div>@endif
    {{ $slot }}
</div>
