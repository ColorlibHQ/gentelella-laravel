{{-- Same payload contract as checkbox; the toggle mirrors it into the hidden input. --}}
<div class="form-group">
    <input type="hidden" name="{{ $field->name }}" value="0">
    <input type="checkbox" class="sr-only" id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
        name="{{ $field->name }}" value="1" @checked((bool) $value)>
    <div class="toggle-row">
        <label for="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}">{{ $field->label }}</label>
        <button type="button" @class(['toggle', 'on' => (bool) $value]) role="switch"
            aria-checked="{{ $value ? 'true' : 'false' }}" aria-label="{{ $field->label }}"
            data-toggle-for="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"></button>
    </div>
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
