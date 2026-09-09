@php $labels = (array) $filter->option('labels', [__('Yes'), __('No')]); @endphp
<div class="form-group">
    <label class="form-label" for="filter-{{ $filter->name }}">{{ $filter->label }}</label>
    <select class="form-control" id="filter-{{ $filter->name }}" data-table-filter="{{ $filter->name }}">
        <option value="">{{ __('Any') }}</option>
        <option value="1">{{ $labels[0] ?? __('Yes') }}</option>
        <option value="0">{{ $labels[1] ?? __('No') }}</option>
    </select>
</div>
