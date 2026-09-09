{{--
    The visible input is a readonly label; data-date-range-name makes the picker
    maintain hidden <name>[from] / <name>[to] inputs, which is what actually
    submits. Validate as an array: ['name.from' => 'nullable|date'].
--}}
@php
    $range = is_array($value) ? $value : [];
    $from = $range['from'] ?? null;
    $to = $range['to'] ?? null;
@endphp
<x-gentelella::field-group :field="$field">
    <div class="date-range" data-date-range data-date-range-name="{{ $field->name }}">
        <input type="text" class="form-control" readonly
            id="field-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name) }}"
            placeholder="{{ $field->option('placeholder', __('Pick a date range')) }}"
            value="{{ $from && $to ? $from.' → '.$to : '' }}">
        <input type="hidden" name="{{ $field->name }}[from]" value="{{ $from }}">
        <input type="hidden" name="{{ $field->name }}[to]" value="{{ $to }}">
    </div>
</x-gentelella::field-group>
