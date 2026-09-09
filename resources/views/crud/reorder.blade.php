@extends('gentelella::page')

@section('title', 'Reorder '.$panel->getPlural())
@section('page_key', $panel->routeName())
@section('content')

<x-gentelella::page-header
    :title="__('Reorder :entity', ['entity' => $panel->getPlural()])"
    :pretitle="ucfirst($panel->getPlural())">
    <x-slot:actions>
        <x-gentelella::btn :href="route($panel->routeName().'.index')">{{ __('Back') }}</x-gentelella::btn>
    </x-slot>
</x-gentelella::page-header>

@include('gentelella::crud.partials.status')

<form method="POST" action="{{ route($panel->routeName().'.reorder') }}">
    @csrf
    <x-gentelella::card>
        {{-- Up/down rather than drag: no library, works on a phone, and can be
             driven from the keyboard. --}}
        <div class="list-group" data-reorder>
            @foreach ($entries as $entry)
                <div class="list-group-item" data-reorder-row>
                    <input type="hidden" name="order[]" value="{{ $entry->getKey() }}">
                    <span>{{ $entry->{$panel->getColumns()[0]->name} ?? $entry->getKey() }}</span>
                    <span class="meta">
                        <button type="button" class="btn btn-outline btn-sm" data-reorder-up
                            aria-label="{{ __('Move up') }}">&uarr;</button>
                        <button type="button" class="btn btn-outline btn-sm" data-reorder-down
                            aria-label="{{ __('Move down') }}">&darr;</button>
                    </span>
                </div>
            @endforeach
        </div>

        <x-slot:footer>
            <div class="form-actions">
                <x-gentelella::btn :href="route($panel->routeName().'.index')">{{ __('Cancel') }}</x-gentelella::btn>
                <x-gentelella::btn type="submit" variant="primary">{{ __('Save order') }}</x-gentelella::btn>
            </div>
        </x-slot>
    </x-gentelella::card>
</form>

@push('scripts')
<script>
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-reorder-up], [data-reorder-down]');
    if (!btn) return;

    const row = btn.closest('[data-reorder-row]');
    const sibling = btn.hasAttribute('data-reorder-up')
        ? row.previousElementSibling
        : row.nextElementSibling;

    if (!sibling) return;

    btn.hasAttribute('data-reorder-up')
        ? row.parentNode.insertBefore(row, sibling)
        : row.parentNode.insertBefore(sibling, row);

    // Keep focus on the button that was pressed, so repeated presses work and
    // the keyboard path does not lose its place.
    btn.focus();
});
</script>
@endpush

@endsection
