{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/kanban.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Kanban')
@section('page_key', 'kanban')
@section('breadcrumb', 'Home > Kanban')

@section('content')
@verbatim
<div class="page-wrapper kanban-page">

  <div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Apps</div>
        <h1 class="page-title">Kanban board</h1>
      </div>
      <div class="page-actions">
        <div class="search-box" style="width:220px">
          <svg class="s-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="5"/><path d="M11 11l3.5 3.5"/></svg>
          <input type="text" id="kanban-filter" placeholder="Filter cards…" aria-label="Filter cards">
        </div>
        <button class="btn btn-outline">Filters</button>
        <button class="btn btn-primary" id="kanban-add-btn">+ New card</button>
      </div>
    </div>
  </div>

  <div id="kanban-board" class="kanban-board"></div>

</div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
import('/src/v4/kanban.js').then(({ initKanban }) => {
  initKanban();
  // Wire the page-action New card button to open the editor with no preset.
  document.getElementById('kanban-add-btn').addEventListener('click', (e) => {
    e.stopPropagation();
    e.preventDefault();
    document.querySelector('.kanban-add[data-add-col="todo"]')?.click();
  });
});
</script>
@endverbatim
@endpush
