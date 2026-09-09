{{--
    Chip / tag. `dismissible` adds the × that main-v4.js animates out on click.
--}}
@props(['tone' => null, 'active' => false, 'dismissible' => false])

<span {{ $attributes->class(['chip', $tone ? 'chip-'.$tone : null, 'active' => $active]) }}>{{ $slot }}@if ($dismissible)<button type="button" class="chip-close" aria-label="{{ __('Remove') }} {{ trim($slot->toHtml()) }}">&times;</button>@endif</span>
