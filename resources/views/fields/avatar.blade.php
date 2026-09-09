@php
    $id = 'field-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name);
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) ($field->option('initial') ?: data_get($entry, 'name')), 0, 1));
@endphp
<div class="form-group">
    <label class="form-label" for="{{ $id }}">{{ $field->label }}</label>
    <label class="avatar-upload" for="{{ $id }}">
        @if ($value)<img src="{{ $value }}" alt="">@else{{ $initial }}@endif
        <span class="overlay">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 7a2 2 0 012-2h2l2-2h6l2 2h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/><circle cx="12" cy="12" r="3.5"/></svg>
        </span>
        <input type="file" id="{{ $id }}" name="{{ $field->name }}" accept="{{ $field->option('accept', 'image/*') }}" hidden>
    </label>
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
