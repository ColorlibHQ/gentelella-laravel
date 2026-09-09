{{--
    An unchecked box sends nothing, so the hidden input keeps the field present
    in the payload — without it, unticking never saves.
--}}
<div class="form-group">
    <input type="hidden" name="{{ $field->name }}" value="0">
    <label class="form-check">
        <input type="checkbox" name="{{ $field->name }}" value="1" @checked((bool) $value)>
        <span>{{ $field->label }}</span>
    </label>
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
