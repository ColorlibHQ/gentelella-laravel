@php
    $items = is_array($value) ? $value : (is_iterable($value) ? iterator_to_array($value) : array_filter([$value]));
    $limit = (int) $column->option('limit', 3);
    $shown = array_slice($items, 0, $limit);
    $extra = count($items) - count($shown);
@endphp
@foreach ($shown as $item)<span class="chip">{{ is_scalar($item) ? $item : json_encode($item) }}</span>@endforeach
@if ($extra > 0)<span class="chip">+{{ $extra }}</span>@endif
