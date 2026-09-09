{{--
    `href` is a closure receiving the model, or an attribute name to read off it.
    A row with no target renders as plain text rather than a dead link.
--}}
@php
    $href = $column->option('href');
    $url = is_callable($href) ? $href($entry) : ($href ? data_get($entry, $href) : null);
@endphp
@if ($url)<a href="{{ $url }}" class="cell-strong">{{ $value }}</a>@else{{ $value }}@endif
