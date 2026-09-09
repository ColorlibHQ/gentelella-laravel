{{--
    GENERATED FILE — DO NOT EDIT.

    Source: production/tables_dynamic.html in ColorlibHQ/gentelella.
    Regenerate: npm run export:demo

    Static markup: the body sits in a verbatim block and compiles to itself.
--}}
@extends('gentelella::page')

@section('title', 'Dynamic tables')
@section('page_key', 'tables-dynamic')
@section('breadcrumb', 'Home > Tables > Dynamic')

@section('content')
@verbatim
<div class="page-header">
    <div class="page-header-row">
      <div>
        <div class="page-pretitle">Tables</div>
        <h1 class="page-title">Server data — orders</h1>
      </div>
      <div class="page-actions">
        <button class="btn btn-outline">Refresh</button>
        <button class="btn btn-primary">Export CSV</button>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Orders</div>
        <div class="card-subtitle">Sortable columns, search across all fields, paginated 25/page.</div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table" data-datatable data-page-length="25">
        <thead>
          <tr>
            <th>Order #</th><th>Customer</th><th>Item</th><th>Qty</th><th>Total</th><th>Channel</th><th>Status</th><th>Date</th>
          </tr>
        </thead>
        <tbody id="orders-tbody"></tbody>
      </table>
    </div>
  </div>
@endverbatim
@endsection

@push('scripts')
@verbatim
<script type="module">
const customers = ['John Doe','Anna Smith','Robert Jones','Emily Wang','Mark Kim','Sarah Lee','Tom Hardy','Lina Park','Diego Reyes','Yuki Tanaka','Chris Hill','Nina Volkov','Ahmed Bashir','Fiona Reilly','Oliver Kane','Priya Verma','Jamie Morales','Sven Kowalski','Rachel Chen','Hana Kawasaki'];
const items = ['Aurora hoodie','Linen shirt','Trail sneakers','Canvas bag','Wireless earbuds','Pour-over set','Desk mat','Mech keyboard','Merino base layer','Coffee grinder'];
const channels = ['Web','Mobile','Email','Partner','Direct'];
const statuses = [['Paid','green'],['Processing','blue'],['Pending','yellow'],['Cancelled','red']];

const months = ['Jan','Feb','Mar','Apr'];
function rng(seed) { let s = seed; return () => { s = (s * 9301 + 49297) % 233280; return s / 233280; }; }
const r = rng(42);
const rows = [];
for (let i = 0; i < 50; i++) {
  const c = customers[Math.floor(r() * customers.length)];
  const it = items[Math.floor(r() * items.length)];
  const ch = channels[Math.floor(r() * channels.length)];
  const [stTxt, stCls] = statuses[Math.floor(r() * statuses.length)];
  const qty = 1 + Math.floor(r() * 5);
  const total = (29 + Math.floor(r() * 470));
  const month = months[Math.floor(r() * months.length)];
  const day = 1 + Math.floor(r() * 28);
  rows.push(`<tr>
    <td class="cell-mono">#${7800 + i}</td>
    <td class="cell-strong">${c}</td>
    <td>${it}</td>
    <td>${qty}</td>
    <td class="cell-strong">$${total}</td>
    <td>${ch}</td>
    <td><span class="status status-${stCls}">${stTxt}</span></td>
    <td>${month} ${day}, 2026</td>
  </tr>`);
}
document.getElementById('orders-tbody').innerHTML = rows.join('');
</script>
@endverbatim
@endpush
