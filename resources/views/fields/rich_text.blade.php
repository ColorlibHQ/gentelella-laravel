{{--
    The editor replaces the wrapper's contents and keeps the hidden textarea in
    sync, so the form submits HTML on the field's own name.

    The value is written unescaped because it is markup by definition. Anything
    stored here is trusted content — sanitise on the way in if it is not.
--}}
<x-gentelella::field-group :field="$field">
    <div class="rich-text" data-rich-text>
        <textarea name="{{ $field->name }}" hidden>{!! $value !!}</textarea>
    </div>
</x-gentelella::field-group>
