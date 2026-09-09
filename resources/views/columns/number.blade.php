@php $n = is_numeric($value) ? number_format((float) $value, (int) $column->option('decimals', 0)) : $value; @endphp
<span class="cell-mono">{{ $n }}</span>
