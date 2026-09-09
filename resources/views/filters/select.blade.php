{{--
    `options` is a value => label map, or read from a model with `model` and
    `attribute`, the same way the select_from_model field does.
--}}
@php
    $options = $filter->option('options');

    if ($options === null && is_string($model = $filter->option('model')) && class_exists($model)) {
        $attribute = (string) $filter->option('attribute', 'name');
        $options = $model::query()
            ->orderBy($attribute)
            ->limit((int) $filter->option('limit', 1000))
            ->pluck($attribute, (new $model)->getKeyName())
            ->all();
    }
@endphp
<div class="form-group">
    <label class="form-label" for="filter-{{ $filter->name }}">{{ $filter->label }}</label>
    <select class="form-control" id="filter-{{ $filter->name }}" data-table-filter="{{ $filter->name }}">
        <option value="">{{ $filter->option('placeholder', __('All')) }}</option>
        @foreach ((array) $options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
