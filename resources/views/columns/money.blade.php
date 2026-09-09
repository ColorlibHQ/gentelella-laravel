@php
    $decimals = (int) $column->option('decimals', 2);
    $amount = is_numeric($value) ? number_format((float) $value, $decimals) : $value;
    $symbol = (string) $column->option('symbol', '');
    $suffix = (string) $column->option('suffix', '');
@endphp
<span class="cell-mono">{{ $symbol }}{{ $amount }}{{ $suffix }}</span>
