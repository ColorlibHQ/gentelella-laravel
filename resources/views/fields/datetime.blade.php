@php
    $formatted = $value instanceof \DateTimeInterface ? $value->format('Y-m-d\TH:i') : $value;
@endphp
<x-gentelella::field-group :field="$field">
    <input class="form-control" type="datetime-local"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        value="{{ $formatted }}"
        @if ($field->isRequired()) required @endif>
</x-gentelella::field-group>
