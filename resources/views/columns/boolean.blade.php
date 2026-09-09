@php
    $on = (bool) $value;
    $labels = (array) $column->option('labels', ['Yes', 'No']);
@endphp
<span class="status status-{{ $on ? 'green' : 'red' }}">{{ $on ? ($labels[0] ?? 'Yes') : ($labels[1] ?? 'No') }}</span>
