{{-- size: sm | lg. tone: azure | red | yellow --}}
@props(['size' => null, 'tone' => null, 'label' => null])

<div {{ $attributes->class(['spinner', $size ? 'spinner-'.$size : null, $tone ? 'spinner-'.$tone : null]) }} role="status">
    @if ($label)<span class="sr-only">{{ $label }}</span>@endif
</div>
