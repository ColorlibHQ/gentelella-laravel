{{-- Switch. main-v4.js toggles .on via delegation. --}}
@props(['on' => false, 'label' => null])

@if ($label)
    <div class="toggle-row">
        <span>{{ $label }}</span>
        <button type="button" {{ $attributes->class(['toggle', 'on' => $on]) }} role="switch" aria-checked="{{ $on ? 'true' : 'false' }}" aria-label="{{ $label }}"></button>
    </div>
@else
    <button type="button" {{ $attributes->class(['toggle', 'on' => $on]) }} role="switch" aria-checked="{{ $on ? 'true' : 'false' }}"></button>
@endif
