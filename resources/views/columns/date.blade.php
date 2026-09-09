@php
    $format = (string) $column->option('format', 'M j, Y');
    $date = null;
    if ($value instanceof \DateTimeInterface) {
        $date = \Illuminate\Support\Carbon::instance($value);
    } elseif (is_string($value) && $value !== '') {
        try { $date = \Illuminate\Support\Carbon::parse($value); } catch (\Throwable) { $date = null; }
    }
@endphp
@if ($date)<time datetime="{{ $date->toIso8601String() }}">{{ $date->format($format) }}</time>@else{{ $value }}@endif
