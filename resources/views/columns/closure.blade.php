{{--
    Computed column. Escaped by default; set 'escape' => false only for markup
    the application itself produced.
--}}
@if ($column->option('escape', true) === false){!! $value !!}@else{{ $value }}@endif
