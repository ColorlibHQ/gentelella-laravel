{{-- A styled file input. The form needs enctype="multipart/form-data", which
     the CRUD form partial always sets. --}}
@php $id = 'field-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name); @endphp
<x-gentelella::field-group :field="$field">
    <div class="file-input">
        <label class="file-input-trigger" for="{{ $id }}">{{ $field->option('button', __('Choose file')) }}</label>
        <span class="file-input-name">{{ $value ? basename((string) $value) : __('No file selected') }}</span>
        <input type="file" id="{{ $id }}" name="{{ $field->name }}"
            @if ($accept = $field->option('accept')) accept="{{ $accept }}" @endif
            @if ($field->option('multiple')) multiple @endif hidden>
    </div>
</x-gentelella::field-group>
