<x-gentelella::field-group :field="$field">
    <input class="form-control" type="range"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        value="{{ $value }}"
        min="{{ $field->option('min', 0) }}"
        max="{{ $field->option('max', 100) }}"
        step="{{ $field->option('step', 1) }}">
</x-gentelella::field-group>
