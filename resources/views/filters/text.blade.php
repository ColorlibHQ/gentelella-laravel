<div class="form-group">
    <label class="form-label" for="filter-{{ $filter->name }}">{{ $filter->label }}</label>
    <input type="text" class="form-control" id="filter-{{ $filter->name }}"
        data-table-filter="{{ $filter->name }}"
        placeholder="{{ $filter->option('placeholder', '') }}">
</div>
