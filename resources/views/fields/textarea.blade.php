<x-gentelella::field-group :field="$field">
    <textarea class="form-control"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        rows="{{ (int) $field->option('rows', 4) }}"
        @if ($field->isRequired()) required @endif>{{ $value }}</textarea>
</x-gentelella::field-group>
