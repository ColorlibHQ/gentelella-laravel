{{--
    Tab strip. Pass `items` as a list of labels, or of ['label' =>, 'active' =>].
    main-v4.js handles the active-class swap via delegation.

    variant: pill | underline
--}}
@props(['items' => [], 'variant' => 'underline', 'active' => 0])

<div {{ $attributes->class(['tabs-'.$variant]) }} role="tablist">
    @foreach ($items as $i => $item)
        @php
            $label = is_array($item) ? ($item['label'] ?? '') : $item;
            $isActive = is_array($item) ? ($item['active'] ?? false) : $i === $active;
        @endphp
        <button @class(['tab', 'active' => $isActive]) role="tab" aria-selected="{{ $isActive ? 'true' : 'false' }}">{{ $label }}</button>
    @endforeach
    {{ $slot }}
</div>
