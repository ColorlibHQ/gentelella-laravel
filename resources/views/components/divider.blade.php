{{-- variant: plain | dashed. A label turns it into a captioned rule. --}}
@props(['variant' => 'plain', 'label' => null])

@if ($label)
    <div {{ $attributes->merge(['class' => 'divider-label']) }}><span>{{ $label }}</span></div>
@else
    <div {{ $attributes->class(['divider-'.$variant]) }} role="separator"></div>
@endif
