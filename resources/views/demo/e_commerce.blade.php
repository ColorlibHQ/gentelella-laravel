{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/e_commerce.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Products')
@section('page_key', 'pages')
@section('breadcrumb', 'Home > Products')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Catalog</div>
        <h1 class="page-title">Products</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline">Categories</button>
        <button class="btn btn-primary">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 8h8M8 4v8"/></svg>
          Add product
        </button>
      </div>
    </div>
  </div>

  <!-- Filter bar -->
  <div class="card" style="margin-bottom:16px">
    <div class="card-body" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;padding:12px 16px">
      <span style="font-size:12px;color:var(--text-muted);font-weight:var(--font-weight-medium)">Filter:</span>
      <span class="chip chip-primary active">Apparel<button type="button" class="chip-close" aria-label="Remove Apparel">×</button></span>
      <span class="chip">Accessories</span>
      <span class="chip">Electronics</span>
      <span class="chip">Home</span>
      <span class="chip">In stock</span>
      <span class="chip chip-yellow active">On sale<button type="button" class="chip-close" aria-label="Remove On sale">×</button></span>
      <span style="margin-left:auto;font-size:12px;color:var(--text-muted)">128 results</span>
      <select class="form-control" style="width:140px;height:32px" aria-label="Sort orders">
        <option>Sort: Newest</option>
        <option>Sort: Price ↑</option>
        <option>Sort: Price ↓</option>
        <option>Sort: Best-selling</option>
      </select>
    </div>
  </div>

  <div class="row col-3" id="products-grid"></div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
import { PRODUCT_IMAGES } from '/src/v4/product-images.js';

const products = [
  ['Aurora hoodie',          'Apparel',       49, 65,  'Sale',     'red',    '★★★★☆ 4.6 (128)', 'hoodie'],
  ['Linen field shirt',      'Apparel',       89, null,'New',      'green',  '★★★★★ 4.9 (42)',  'shirt'],
  ['Trail runner sneakers',  'Footwear',      129,159, 'Sale',     'red',    '★★★★☆ 4.4 (231)', 'sneakers'],
  ['Canvas weekend bag',     'Accessories',   79, null,null,       null,     '★★★★☆ 4.5 (76)',  'bag'],
  ['Wireless earbuds Pro',   'Electronics',   149,null,'Hot',      'yellow', '★★★★★ 5.0 (892)', 'earbuds'],
  ['Ceramic pour-over set',  'Home',          54, null,null,       null,     '★★★★☆ 4.7 (61)',  'pourover'],
  ['Standing desk mat',      'Home',          39, 49,  'Sale',     'red',    '★★★★☆ 4.3 (158)', 'mat'],
  ['Mechanical keyboard',    'Electronics',   189,null,'New',      'green',  '★★★★★ 4.8 (304)', 'keyboard'],
  ['Merino base layer',      'Apparel',       69, null,null,       null,     '★★★★☆ 4.6 (97)',  'baselayer']
];

document.getElementById('products-grid').innerHTML = products.map(([name, cat, price, oldPrice, badge, badgeCls, stars, slug]) => `
  <a class="product-card" href="product_detail.html">
    <div class="product-thumb">
      ${badge ? `<span class="product-badge"><span class="chip chip-${badgeCls}">${badge}</span></span>` : ''}
      ${PRODUCT_IMAGES[slug] || ''}
    </div>
    <div class="product-info">
      <div class="name">${name}</div>
      <div class="category">${cat}</div>
      <div class="price-row">
        <span class="price">$${price}</span>
        ${oldPrice ? `<span class="price-old">$${oldPrice}</span>` : ''}
      </div>
      <div class="stars">${stars}</div>
    </div>
  </a>
`).join('');
</script>
@endverbatim
@endpush
