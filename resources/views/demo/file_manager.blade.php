{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/file_manager.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Files')
@section('page_key', 'files')
@section('breadcrumb', 'Home > Files')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Apps</div>
        <h1 class="page-title">Files</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline" id="new-folder-btn">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4a1 1 0 011-1h3l2 2h5a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V4z"/><path d="M8 7v4M6 9h4"/></svg>
          New folder
        </button>
        <button class="btn btn-primary" id="upload-btn">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 11v3H2v-3"/><path d="M11 6L8 3 5 6"/><line x1="8" y1="3" x2="8" y2="11"/></svg>
          Upload
        </button>
      </div>
    </div>
  </div>

  <div class="card file-manager">
    <div class="fm-layout">
      <!-- Tree sidebar -->
      <aside class="fm-tree-wrap">
        <div id="fm-tree" class="fm-tree"></div>

        <div class="fm-storage">
          <div class="fm-storage-label">Storage</div>
          <div class="storage-bar"><div style="width:62%;background:var(--primary)"></div></div>
          <div class="fm-storage-meta">6.2 GB of 10 GB used</div>
          <button class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:8px">Upgrade plan</button>
        </div>
      </aside>

      <!-- Main area -->
      <section class="fm-main">
        <div class="fm-toolbar">
          <nav class="fm-breadcrumb breadcrumb" id="fm-breadcrumb" aria-label="Path"></nav>
          <div class="fm-toolbar-actions">
            <div class="search-box" style="width:200px">
              <svg class="s-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="5"/><path d="M11 11l3.5 3.5"/></svg>
              <input type="text" placeholder="Search files…" aria-label="Search files">
            </div>
            <div class="fm-view-toggle">
              <button type="button" class="view-btn active" id="view-grid" aria-label="Grid view">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="5" height="5" rx="1"/><rect x="9" y="2" width="5" height="5" rx="1"/><rect x="2" y="9" width="5" height="5" rx="1"/><rect x="9" y="9" width="5" height="5" rx="1"/></svg>
              </button>
              <button type="button" class="view-btn" id="view-list" aria-label="List view">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="2" y1="4" x2="14" y2="4"/><line x1="2" y1="8" x2="14" y2="8"/><line x1="2" y1="12" x2="14" y2="12"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div id="fm-grid" class="fm-grid view-grid"></div>

        <div id="fm-empty" class="fm-empty" hidden>
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7a2 2 0 012-2h4l2 2h7a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
          <div class="empty-title">This folder is empty</div>
          <div class="empty-desc">Drag files here or use the Upload button to add files.</div>
        </div>
      </section>
    </div>
  </div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
import('/src/v4/file-manager.js').then(({ initFileManager }) => initFileManager());
</script>
@endverbatim
@endpush
