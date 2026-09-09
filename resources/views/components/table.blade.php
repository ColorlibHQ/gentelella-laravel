{{--
    Table in its scroll container. `datatable` opts into the enhancement that
    src/v4/tables.js applies to [data-datatable] — sorting, search, paging,
    row selection and CSV export.
--}}
@props([
    'datatable' => false,
    'pageLength' => null,
    'selectable' => false,
    'export' => null,
    'responsive' => true,
])

@if ($responsive)<div class="table-responsive">@endif
<table {{ $attributes->merge(['class' => 'table']) }}
    @if ($datatable) data-datatable @endif
    @if ($pageLength) data-page-length="{{ (int) $pageLength }}" @endif
    @if ($selectable) data-selectable @endif
    @if ($export) data-export="{{ $export }}" @endif
>{{ $slot }}</table>
@if ($responsive)</div>@endif
