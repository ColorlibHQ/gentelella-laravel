{{-- Inline notice. tone: null (info) | warning | danger | success --}}
@props(['tone' => null])

<div {{ $attributes->class(['banner', $tone ? 'banner-'.$tone : null]) }} role="{{ in_array($tone, ['warning', 'danger'], true) ? 'alert' : 'status' }}">
    @isset($icon){{ $icon }}@endisset
    <div class="banner-body">{{ $slot }}</div>
    @isset($actions)<div class="banner-actions">{{ $actions }}</div>@endisset
</div>
