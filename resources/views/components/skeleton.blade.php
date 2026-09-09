{{-- Loading placeholder. variant: text | text-lg | rect | circle --}}
@props(['variant' => 'text'])

<div {{ $attributes->class(['skeleton', 'skeleton-'.$variant]) }} aria-hidden="true"></div>
