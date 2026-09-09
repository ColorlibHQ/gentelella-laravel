{{-- Page title block. The `actions` slot sits on the right of the title row. --}}
@props(['title', 'pretitle' => null])

<div {{ $attributes->merge(['class' => 'page-header']) }}>
    <div class="page-header-row">
        <div>
            @if ($pretitle)<div class="page-pretitle">{{ $pretitle }}</div>@endif
            <h1 class="page-title">{{ $title }}</h1>
        </div>
        @isset($actions)<div class="page-actions">{{ $actions }}</div>@endisset
    </div>
    {{ $slot }}
</div>
