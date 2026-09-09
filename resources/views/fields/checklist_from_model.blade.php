{{--
    A set of checkboxes submitting as name[]. The hidden empty value keeps the
    key present when nothing is ticked, so clearing every box actually saves.
--}}
@php
    $options = (array) $field->option('options', []);
    $model = $field->option('model');

    if ($options === [] && is_string($model) && class_exists($model)) {
        $attribute = (string) $field->option('attribute', 'name');
        $options = $model::query()->orderBy($attribute)
            ->limit((int) $field->option('limit', 1000))
            ->pluck($attribute, (new $model)->getKeyName())->all();
    }

    $checked = collect(is_iterable($value) ? $value : array_filter([$value]))
        ->map(fn ($v) => is_object($v) ? (string) $v->getKey() : (string) $v)
        ->all();
@endphp
<div class="form-group">
    <label class="form-label">{{ $field->label }}</label>
    <input type="hidden" name="{{ $field->name }}" value="">
    <div class="form-check-group" role="group" aria-label="{{ $field->label }}">
        @foreach ($options as $key => $label)
            <label class="form-check">
                <input type="checkbox" name="{{ $field->name }}[]" value="{{ $key }}"
                    @checked(in_array((string) $key, $checked, true))>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
