{{--
    A repeating group of sub-fields, submitting as name[0][sub], name[1][sub], …

        ['name' => 'items', 'type' => 'repeatable', 'fields' => [
            ['name' => 'label', 'type' => 'text'],
            ['name' => 'qty',   'type' => 'number'],
        ]]

    Rows are cloned from a <template>. Indices are renumbered on every add and
    remove, so deleting a middle row does not leave a gap that PHP would read as
    a string-keyed array.
--}}
@php
    $subFields = collect((array) $field->option('fields', []))
        ->map(fn ($definition) => \ColorlibHQ\Gentelella\Crud\Field::fromArray($definition));

    $rows = collect(is_iterable($value) ? $value : [])->values();
    $id = 'repeat-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $field->name);
@endphp

<div class="form-group">
    <label class="form-label">{{ $field->label }}</label>

    <div class="repeatable" id="{{ $id }}" data-repeatable="{{ $field->name }}">
        <div class="repeatable-rows">
            @foreach ($rows as $i => $row)
                <div class="repeatable-row" data-repeatable-row>
                    <div class="form-row">
                        @foreach ($subFields as $sub)
                            <div class="form-group">
                                <label class="form-label">{{ $sub->label }}</label>
                                <input class="form-control" type="{{ $sub->type === 'number' ? 'number' : 'text' }}"
                                    name="{{ $field->name }}[{{ $i }}][{{ $sub->name }}]"
                                    value="{{ data_get($row, $sub->name) }}">
                            </div>
                        @endforeach
                        <div class="form-group form-actions">
                            <button type="button" class="btn btn-outline btn-sm" data-repeatable-remove>{{ __('Remove') }}</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-repeatable-template>
            <div class="repeatable-row" data-repeatable-row>
                <div class="form-row">
                    @foreach ($subFields as $sub)
                        <div class="form-group">
                            <label class="form-label">{{ $sub->label }}</label>
                            <input class="form-control" type="{{ $sub->type === 'number' ? 'number' : 'text' }}"
                                name="{{ $field->name }}[__INDEX__][{{ $sub->name }}]" value="">
                        </div>
                    @endforeach
                    <div class="form-group form-actions">
                        <button type="button" class="btn btn-outline btn-sm" data-repeatable-remove>{{ __('Remove') }}</button>
                    </div>
                </div>
            </div>
        </template>

        <button type="button" class="btn btn-outline btn-sm" data-repeatable-add>
            {{ $field->option('add_label', __('Add row')) }}
        </button>
    </div>

    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>

@once
@push('scripts')
<script>
// One delegated handler for every repeatable on the page.
document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-repeatable-add]');
    const remove = e.target.closest('[data-repeatable-remove]');
    if (!add && !remove) return;

    const root = (add || remove).closest('[data-repeatable]');
    if (!root) return;

    if (add) {
        const tpl = root.querySelector('[data-repeatable-template]');
        root.querySelector('.repeatable-rows').appendChild(tpl.content.cloneNode(true));
    } else {
        remove.closest('[data-repeatable-row]').remove();
    }

    // Renumber, so removing a middle row leaves a contiguous list.
    root.querySelectorAll('[data-repeatable-row]').forEach((row, i) => {
        row.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace(/\[(?:\d+|__INDEX__)\]/, '[' + i + ']');
        });
    });
});
</script>
@endpush
@endonce
