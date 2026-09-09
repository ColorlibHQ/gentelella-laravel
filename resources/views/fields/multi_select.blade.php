{{--
    Chips with autocomplete. The hidden <select multiple> is what the form
    submits; the control keeps its selected options in step.
--}}
@php
    $options = (array) $field->option('options', []);
    $model = $field->option('model');

    if ($options === [] && is_string($model) && class_exists($model)) {
        $attribute = (string) $field->option('attribute', 'name');
        $options = $model::query()
            ->orderBy($attribute)
            ->limit((int) $field->option('limit', 1000))
            ->pluck($attribute, (new $model)->getKeyName())
            ->all();
    }

    $selected = collect(is_iterable($value) ? $value : array_filter([$value]))
        ->map(fn ($v) => is_object($v) ? ($v->getKey() ?? (string) $v) : $v)
        ->map(fn ($v) => (string) $v)
        ->all();
@endphp
<x-gentelella::field-group :field="$field">
    <div class="multi-select" data-multi-select>
        <select name="{{ $field->name }}[]" multiple hidden>
            @foreach ($options as $key => $label)
                <option value="{{ $key }}" @selected(in_array((string) $key, $selected, true))>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</x-gentelella::field-group>
