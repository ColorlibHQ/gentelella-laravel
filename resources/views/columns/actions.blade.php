{{--
    Per-row edit and delete. Each button is only rendered when its route exists,
    so a panel that only lists records does not show controls that 404.
--}}
@php
    $name = (string) $column->option('route');
    $editRoute = $name.'.edit';
    $destroyRoute = $name.'.destroy';
@endphp
<div class="row-actions">
    @if (\Illuminate\Support\Facades\Route::has($editRoute))
        <a class="btn btn-outline btn-sm" href="{{ route($editRoute, $entry->getKey()) }}">{{ __('Edit') }}</a>
    @endif
    @if (\Illuminate\Support\Facades\Route::has($destroyRoute))
        <form method="POST" action="{{ route($destroyRoute, $entry->getKey()) }}" class="row-action-form"
              data-confirm="{{ __('Delete this record? This cannot be undone.') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
        </form>
    @endif
</div>
