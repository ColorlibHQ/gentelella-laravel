{{--
    Card. The header only renders when there is something to put in it, so
    <x-gentelella::card> alone gives you a plain bordered box.

    Set `flush` for content that manages its own padding — a table-responsive
    wrapper, say — to skip the .card-body.
--}}
@props(['title' => null, 'subtitle' => null, 'bodyClass' => null, 'flush' => false])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $subtitle || isset($options))
        <div class="card-header">
            <div>
                @if ($title)<div class="card-title">{{ $title }}</div>@endif
                @if ($subtitle)<div class="card-subtitle">{{ $subtitle }}</div>@endif
            </div>
            @isset($options)<div class="card-options">{{ $options }}</div>@endisset
        </div>
    @endif

    @if ($flush)
        {{ $slot }}
    @else
        <div @class(array_filter(['card-body', $bodyClass]))>{{ $slot }}</div>
    @endif

    @isset($footer)<div class="card-footer">{{ $footer }}</div>@endisset
</div>
