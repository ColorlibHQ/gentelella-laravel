{{--
    `options` is a name => label map. `allows_null` adds a blank first entry so
    an optional relationship can be cleared.
--}}
@php $options = (array) $field->option('options', []); @endphp
<x-gentelella::field-group :field="$field">
    <select class="form-control"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        @if ($field->isRequired()) required @endif>
        @if ($field->option('allows_null', ! $field->isRequired()))
            <option value="">{{ $field->option('placeholder', '—') }}</option>
        @endif
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected((string) $key === (string) $value)>{{ $label }}</option>
        @endforeach
    </select>
</x-gentelella::field-group>
