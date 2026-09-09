{{--
    The wrapper every field type shares: label, control, validation error, hint.
    Kept in one place so a change to error styling lands on all of them at once.
--}}
@props(['field'])
@php $id = 'field-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name); @endphp
<div class="form-group">
    <label class="form-label" for="{{ $id }}">
        {{ $field->label }}@if ($field->isRequired())<span aria-hidden="true"> *</span><span class="sr-only"> ({{ __('required') }})</span>@endif
    </label>
    {{ $slot }}
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
