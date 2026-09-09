@extends('gentelella::page')

@section('title', ucfirst($panel->getPlural()))
@section('page_key', $panel->routeName())
@section('content')

<x-gentelella::page-header :title="ucfirst($panel->getPlural())">
    <x-slot:actions>
        @if ($panel->isReorderable() && \Illuminate\Support\Facades\Route::has($panel->routeName().'.reorder'))
            <x-gentelella::btn :href="route($panel->routeName().'.reorder')">{{ __('Reorder') }}</x-gentelella::btn>
        @endif
        @if (\Illuminate\Support\Facades\Route::has($panel->routeName().'.create'))
            <x-gentelella::btn variant="primary" :href="route($panel->routeName().'.create')">
                {{ __('New :entity', ['entity' => $panel->getSingular()]) }}
            </x-gentelella::btn>
        @endif
    </x-slot>
</x-gentelella::page-header>

@include('gentelella::crud.partials.status')

<x-gentelella::card flush>
    @include('gentelella::crud.partials.filters')

    {{-- Rows arrive from the JSON endpoint, so the first paint never waits on
         the query. tables.js reads data-ajax and switches DataTables into
         server-side mode. --}}
    {{-- data-export-url is what tables.js needs for a server-side export: the
         browser holds one page, so a client-side CSV would export a fraction of
         the results and look like it exported everything. --}}
    <x-gentelella::table
        datatable
        export="{{ $panel->getPlural() }}"
        :page-length="25"
        :data-ajax="route($panel->routeName().'.data')"
        :data-export-url="\Illuminate\Support\Facades\Route::has($panel->routeName().'.export') ? route($panel->routeName().'.export') : null">
        <thead>
            <tr>
                @foreach ($panel->getColumns() as $column)
                    <th @if (! $panel->isOrderable($column)) data-orderable="false" @endif>{{ $column->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
    </x-gentelella::table>
</x-gentelella::card>

@endsection
