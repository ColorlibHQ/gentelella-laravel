{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/media_gallery.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Media')
@section('page_key', 'media')
@section('breadcrumb', 'Home > Media')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Library</div>
        <h1 class="page-title">Media gallery</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline">Filter</button>
        <button class="btn btn-primary">Upload</button>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="media-grid" id="media-grid"></div>
    </div>
  </div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
const palettes = [
  ['linear-gradient(135deg,#1ABB9C,#169f85)', 'hero-bg.jpg', '2.4 MB'],
  ['linear-gradient(135deg,#066fd1,#4299e1)', 'team.jpg',    '1.8 MB'],
  ['linear-gradient(135deg,#f59f00,#f76707)', 'sunset.jpg',  '3.1 MB'],
  ['linear-gradient(135deg,#ae3ec9,#d6336c)', 'product.png', '0.9 MB'],
  ['linear-gradient(135deg,#2fb344,#74b816)', 'forest.jpg',  '4.2 MB'],
  ['linear-gradient(135deg,#d63939,#f76707)', 'fire.jpg',    '2.7 MB'],
  ['linear-gradient(135deg,#17a2b8,#066fd1)', 'ocean.jpg',   '5.5 MB'],
  ['linear-gradient(135deg,#1a2332,#626d7d)', 'night.jpg',   '3.3 MB'],
  ['linear-gradient(135deg,#4263eb,#ae3ec9)', 'aurora.png',  '1.2 MB'],
  ['linear-gradient(135deg,#74b816,#2fb344)', 'meadow.jpg',  '2.0 MB'],
  ['linear-gradient(135deg,#d6336c,#ae3ec9)', 'bloom.jpg',   '1.5 MB'],
  ['linear-gradient(135deg,#f59f00,#d6336c)', 'desert.jpg',  '4.8 MB']
];
document.getElementById('media-grid').innerHTML = palettes.map(([bg, name, size]) => `
  <div class="media-tile" style="background:${bg}">
    <div class="meta"><span>${name}</span><span class="size">${size}</span></div>
  </div>
`).join('');
</script>
@endverbatim
@endpush
