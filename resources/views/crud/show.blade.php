@extends('gentelella::page')

@section('title', ucfirst($panel->getSingular()))
@section('page_key', $panel->routeName())
@section('content')

@php $renderer = app(\ColorlibHQ\Gentelella\Crud\ColumnRenderer::class); @endphp

<x-gentelella::page-header
    :title="ucfirst($panel->getSingular()).' #'.$entry->getKey()"
    :pretitle="ucfirst($panel->getPlural())">
    <x-slot:actions>
        @if (\Illuminate\Support\Facades\Route::has($panel->routeName().'.edit'))
            <x-gentelella::btn variant="primary" :href="route($panel->routeName().'.edit', $entry->getKey())">
                {{ __('Edit') }}
            </x-gentelella::btn>
        @endif
    </x-slot>
</x-gentelella::page-header>

<x-gentelella::card flush>
    <x-gentelella::table :responsive="false">
        <tbody>
            @foreach ($panel->getColumns() as $column)
                @continue($column->type === 'actions')
                <tr>
                    <th scope="row" style="width:220px">{{ $column->label }}</th>
                    <td>{!! $renderer->render($column, $entry) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </x-gentelella::table>
</x-gentelella::card>

@endsection
