@php $options = (array) $field->option('options', []); @endphp
<x-gentelella::field-group :field="$field">
    <div class="form-check-group" role="radiogroup" aria-label="{{ $field->label }}">
        @foreach ($options as $key => $label)
            <label class="form-check">
                <input type="radio" name="{{ $field->name }}" value="{{ $key }}" @checked((string) $key === (string) $value)>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</x-gentelella::field-group>
