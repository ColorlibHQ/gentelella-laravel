{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/form_advanced.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Advanced form')
@section('page_key', 'form-advanced')
@section('breadcrumb', 'Home > Forms > Advanced')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Forms</div>
        <h1 class="page-title">Advanced controls</h1>
      </div>
    </div>
  </div>

  <div class="row col-2">
    <div class="card">
      <div class="card-header"><div class="card-title">Date & time</div></div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Date</label><input type="date" class="form-control" aria-label="Date"></div>
          <div class="form-group"><label class="form-label">Time</label><input type="time" class="form-control" aria-label="Time"></div>
        </div>
        <div class="form-group"><label class="form-label">Date & time</label><input type="datetime-local" class="form-control" aria-label="Date and time"></div>
        <div class="form-group"><label class="form-label">Month</label><input type="month" class="form-control" aria-label="Month"></div>
        <div class="form-group"><label class="form-label">Week</label><input type="week" class="form-control" aria-label="Week"></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title">Numeric</div></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">Range slider</label>
          <input type="range" class="form-control" min="0" max="100" value="42" aria-label="Range slider" style="height:auto;padding:0;border:none;background:transparent">
          <div class="form-help">Native HTML5 range — value: 42</div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Number</label><input type="number" class="form-control" value="2026" min="2000" max="2100" aria-label="Number"></div>
          <div class="form-group"><label class="form-label">Stepper</label><input type="number" class="form-control" value="0.5" step="0.1" aria-label="Stepper"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Color</label>
          <input type="color" class="form-control" value="#1ABB9C" aria-label="Color" style="padding:2px;height:36px">
        </div>
      </div>
    </div>
  </div>

  <div class="row col-1">
    <div class="card">
      <div class="card-header"><div class="card-title">Multi-select & search</div></div>
      <div class="card-body">
        <div class="form-row cols-3">
          <div class="form-group">
            <label class="form-label">Country</label>
            <select class="form-control" aria-label="Country">              <option>Latvia</option><option>Estonia</option><option>Lithuania</option>
              <option>Finland</option><option>Sweden</option><option>Norway</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tags (multi)</label>
            <select class="form-control" multiple size="3" aria-label="Tags">
              <option selected>design</option><option>engineering</option><option selected>product</option>
              <option>marketing</option><option>sales</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Search</label>
            <div class="input-group">
              <svg class="input-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="5"/><path d="M11 11l3.5 3.5"/></svg>
              <input type="search" class="form-control" placeholder="Search teams, people, projects…" aria-label="Search">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Selected tags</label>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <span class="chip chip-primary">design ×</span>
            <span class="chip chip-blue">product ×</span>
            <span class="chip chip-yellow">priority ×</span>
            <span class="chip">+ add tag</span>
          </div>
        </div>
      </div>
    </div>
  </div>
@endverbatim
@endsection
