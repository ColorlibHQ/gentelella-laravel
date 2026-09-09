{{-- Two native date inputs; the table sends them as filters[name][from|to]. --}}
<div class="form-group">
    <label class="form-label" for="filter-{{ $filter->name }}-from">{{ $filter->label }}</label>
    <div class="form-row">
        <input type="date" class="form-control" id="filter-{{ $filter->name }}-from"
            data-table-filter="{{ $filter->name }}" data-table-filter-part="from"
            aria-label="{{ $filter->label }} — {{ __('from') }}">
        <input type="date" class="form-control" id="filter-{{ $filter->name }}-to"
            data-table-filter="{{ $filter->name }}" data-table-filter-part="to"
            aria-label="{{ $filter->label }} — {{ __('to') }}">
    </div>
</div>
