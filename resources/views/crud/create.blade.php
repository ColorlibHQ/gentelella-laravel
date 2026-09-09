@extends('gentelella::page')

@section('title', 'New '.$panel->getSingular())
@section('page_key', $panel->routeName())
@section('content')

<x-gentelella::page-header
    :title="__('New :entity', ['entity' => $panel->getSingular()])"
    :pretitle="ucfirst($panel->getPlural())" />

@include('gentelella::crud.partials.form', [
    'action' => route($panel->routeName().'.store'),
    'method' => 'POST',
])

@endsection
