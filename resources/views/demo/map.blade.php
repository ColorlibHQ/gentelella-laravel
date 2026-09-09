{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/map.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Map')
@section('page_key', 'map')
@section('breadcrumb', 'Home > Map')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Geo</div>
        <h1 class="page-title">Customer map</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline">Filters</button>
      </div>
    </div>
  </div>

  <div class="row col-8-4">
    <div class="card">
      <div id="map" style="height:520px;border-radius:var(--radius-lg)"></div>
    </div>

    <div class="card">
      <div class="card-header"><div><div class="card-title">Top regions</div><div class="card-subtitle">Active customers by city</div></div></div>
      <div class="card-body" style="padding:8px 16px">
        <div class="visitor-row"><span class="visitor-flag">🇺🇸</span><span class="visitor-name">San Francisco</span><span class="visitor-pct">182</span><div class="visitor-bar"><div class="fill" style="width:82%"></div></div></div>
        <div class="visitor-row"><span class="visitor-flag">🇬🇧</span><span class="visitor-name">London</span><span class="visitor-pct">147</span><div class="visitor-bar"><div class="fill" style="width:64%"></div></div></div>
        <div class="visitor-row"><span class="visitor-flag">🇩🇪</span><span class="visitor-name">Berlin</span><span class="visitor-pct">98</span><div class="visitor-bar"><div class="fill" style="width:45%"></div></div></div>
        <div class="visitor-row"><span class="visitor-flag">🇯🇵</span><span class="visitor-name">Tokyo</span><span class="visitor-pct">76</span><div class="visitor-bar"><div class="fill" style="width:35%"></div></div></div>
        <div class="visitor-row"><span class="visitor-flag">🇧🇷</span><span class="visitor-name">São Paulo</span><span class="visitor-pct">52</span><div class="visitor-bar"><div class="fill" style="width:24%"></div></div></div>
        <div class="visitor-row"><span class="visitor-flag">🇱🇻</span><span class="visitor-name">Riga</span><span class="visitor-pct">31</span><div class="visitor-bar"><div class="fill" style="width:14%"></div></div></div>
      </div>
    </div>
  </div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
import('leaflet').then(({ default: L }) => {
  const map = L.map('map').setView([30, 10], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18
  }).addTo(map);
  const cities = [
    [37.7749, -122.4194, 'San Francisco', 182],
    [51.5074, -0.1278,   'London',        147],
    [52.5200, 13.4050,   'Berlin',         98],
    [35.6762, 139.6503,  'Tokyo',          76],
    [-23.5505, -46.6333, 'São Paulo',      52],
    [56.9496, 24.1052,   'Riga',           31]
  ];
  cities.forEach(([lat, lng, name, count]) => {
    L.circleMarker([lat, lng], {
      radius: Math.max(6, Math.sqrt(count) * 1.5),
      color: '#1ABB9C', fillColor: '#1ABB9C', fillOpacity: 0.4, weight: 2
    }).addTo(map).bindPopup(`<strong>${name}</strong><br>${count} customers`);
  });
});
</script>
@endverbatim
@endpush
