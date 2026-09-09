<x-gentelella::field-group :field="$field">
    <input class="form-control" type="month"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        value="{{ $value }}"
        @if ($field->isRequired()) required @endif>
</x-gentelella::field-group>
