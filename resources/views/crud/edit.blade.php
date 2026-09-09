@extends('gentelella::page')

@section('title', 'Edit '.$panel->getSingular())
@section('page_key', $panel->routeName())
@section('content')

<x-gentelella::page-header
    :title="__('Edit :entity', ['entity' => $panel->getSingular()])"
    :pretitle="ucfirst($panel->getPlural())" />

@include('gentelella::crud.partials.form', [
    'action' => route($panel->routeName().'.update', $entry->getKey()),
    'method' => 'PUT',
])

@endsection
