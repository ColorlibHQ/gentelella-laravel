{{-- The "…" button in a card header. main-v4.js opens the popover menu. --}}
@props(['label' => 'Card options'])

<button {{ $attributes->merge(['class' => 'card-opt-btn']) }} type="button" aria-label="{{ $label }}">
    @if ($slot->isEmpty())
        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="7" cy="3" r="1"/><circle cx="7" cy="7" r="1"/><circle cx="7" cy="11" r="1"/></svg>
    @else
        {{ $slot }}
    @endif
</button>
