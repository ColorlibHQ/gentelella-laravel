{{--
    Status pill. `tones` maps a value to a colour; anything unmapped falls back
    to `default_tone`, so a new enum case shows up as a neutral pill instead of
    an unstyled one.
--}}
@php
    $tones = (array) $column->option('tones', []);
    $key = is_scalar($value) ? (string) $value : '';
    $tone = $tones[$key] ?? $column->option('default_tone', 'blue');
    $labels = (array) $column->option('labels', []);
@endphp
<span class="status status-{{ preg_replace('/[^a-z]/', '', (string) $tone) }}">{{ $labels[$key] ?? $value }}</span>
