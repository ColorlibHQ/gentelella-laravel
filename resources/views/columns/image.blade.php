@php $alt = (string) ($column->option('alt') ? data_get($entry, $column->option('alt')) : ''); @endphp
@if ($value)<img src="{{ $value }}" alt="{{ $alt }}" class="cell-avatar" loading="lazy">@endif
