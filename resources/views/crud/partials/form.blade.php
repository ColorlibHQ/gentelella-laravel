{{-- Shared by create and edit; only the method and action differ. --}}
@php $renderer = app(\ColorlibHQ\Gentelella\Crud\FieldRenderer::class); @endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')@method($method)@endif

    <x-gentelella::card>
        @if ($errors->any())
            <x-gentelella::banner tone="danger">
                {{ __('Please correct the errors below.') }}
            </x-gentelella::banner>
        @endif

        @foreach ($panel->getFields() as $field)
            {!! $renderer->render($field, $entry) !!}
        @endforeach

        <x-slot:footer>
            <div class="form-actions">
                <x-gentelella::btn :href="route($panel->routeName().'.index')">{{ __('Cancel') }}</x-gentelella::btn>
                <x-gentelella::btn type="submit" variant="primary">{{ __('Save') }}</x-gentelella::btn>
            </div>
        </x-slot>
    </x-gentelella::card>
</form>
