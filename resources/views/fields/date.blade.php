<x-gentelella::field-group :field="$field">
    <input class="form-control" type="date"
        id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}"
        value="{{ $value }}"
        @if ($field->isRequired()) required @endif
        @foreach ((array) $field->option('attributes', []) as $k => $v) {{ $k }}="{{ $v }}" @endforeach>
</x-gentelella::field-group>
