{{-- Default column. `strong` and `mono` map to the table cell helper classes. --}}
@if ($column->option('strong'))<span class="cell-strong">{{ $value }}</span>
@elseif ($column->option('mono'))<span class="cell-mono">{{ $value }}</span>
@else{{ $value }}
@endif
