{{--
    A belongsTo picker. Reads its options from the related model:

        ['name' => 'category_id', 'type' => 'select_from_model',
         'model' => Category::class, 'attribute' => 'title']

    Ordered by the displayed attribute so the list is predictable, and capped so
    a large table cannot render a hundred thousand <option> elements — past that
    point the right control is a search field, not a select.
--}}
@php
    $model = $field->option('model');
    $attribute = (string) $field->option('attribute', 'name');
    $limit = (int) $field->option('limit', 1000);

    $options = $field->option('options');

    if ($options === null && is_string($model) && class_exists($model)) {
        $query = $model::query()->orderBy($attribute)->limit($limit);

        if (is_callable($scope = $field->option('scope'))) {
            $scope($query);
        }

        $options = $query->pluck($attribute, (new $model)->getKeyName())->all();
    }
@endphp
@include('gentelella::fields.select', [
    'field' => new \ColorlibHQ\Gentelella\Crud\Field(
        $field->name, 'select', $field->label, $field->rules, $field->hint, $field->default,
        array_merge($field->options, ['options' => (array) ($options ?? [])]),
    ),
    'entry' => $entry,
    'value' => $value,
])
