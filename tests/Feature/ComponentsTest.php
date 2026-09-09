<?php

declare(strict_types=1);

it('omits the card header when there is nothing to put in it', function () {
    expect(render('<x-gentelella::card>body</x-gentelella::card>'))
        ->not->toContain('card-header')
        ->toContain('<div class="card-body">body</div>');
});

it('renders a card header for a title and subtitle', function () {
    expect(render('<x-gentelella::card title="All customers" subtitle="Sortable">x</x-gentelella::card>'))
        ->toContain('<div class="card-title">All customers</div>')
        ->toContain('<div class="card-subtitle">Sortable</div>');
});

it('drops the card body wrapper when flushed', function () {
    $html = render('<x-gentelella::card flush>raw</x-gentelella::card>');

    expect($html)->toContain('raw')->not->toContain('card-body');
});

it('merges consumer classes onto the card', function () {
    expect(render('<x-gentelella::card class="chart-card">x</x-gentelella::card>'))
        ->toContain('class="card chart-card"');
});

it('renders a button by default and a link when given an href', function () {
    expect(render('<x-gentelella::btn>Go</x-gentelella::btn>'))
        ->toContain('<button type="button"')
        ->toContain('class="btn btn-outline"');

    expect(render('<x-gentelella::btn href="/x" variant="primary" size="sm">Go</x-gentelella::btn>'))
        ->toContain('<a href="/x"')
        ->toContain('btn btn-primary btn-sm');
});

it('clamps a progress value and strips anything unsafe from the tone', function () {
    expect(render('<x-gentelella::progress :value="140" />'))->toContain('width:100%')
        ->and(render('<x-gentelella::progress :value="-20" />'))->toContain('width:0%')
        ->and(render('<x-gentelella::progress :value="42.5" />'))->toContain('width:42.5%');

    // A tone lands inside an inline style, so it must not be able to escape it.
    expect(render('<x-gentelella::progress value="1" tone="red;background:url(x)" />'))
        ->toContain('var(--redbackgroundurlx)')
        ->not->toContain('url(x)');
});

it('exposes progress state to assistive tech', function () {
    expect(render('<x-gentelella::progress :value="30" />'))
        ->toContain('role="progressbar"')
        ->toContain('aria-valuenow="30"');
});

it('infers the stat change direction from the sign', function () {
    expect(render('<x-gentelella::stat label="Users" value="2,500" change="12%" />'))
        ->toContain('stat-change up');

    expect(render('<x-gentelella::stat label="Users" value="2,500" change="-4%" />'))
        ->toContain('stat-change down');

    expect(render('<x-gentelella::stat label="U" value="1" change="-4%" direction="up" />'))
        ->toContain('stat-change up');
});

it('omits the stat change and sparkline when not supplied', function () {
    $html = render('<x-gentelella::stat label="Users" value="2,500" />');

    expect($html)->not->toContain('stat-change')
        ->not->toContain('stat-spark')
        ->toContain('<div class="stat-label">Users</div>');
});

it('clamps sparkline bar heights', function () {
    $html = render('<x-gentelella::stat label="U" value="1" :spark="[40, 250, -5]" />');

    expect($html)->toContain('height:40%')
        ->toContain('height:100%')
        ->toContain('height:0%');
});

it('renders an avatar initial or an image', function () {
    expect(render('<x-gentelella::avatar name="ada lovelace" />'))->toContain('>A')
        ->and(render('<x-gentelella::avatar name="Ada" size="lg" />'))->toContain('class="avatar avatar-lg"')
        ->and(render('<x-gentelella::avatar name="Ada" src="/a.png" />'))->toContain('<img src="/a.png" alt="Ada">')
        ->and(render('<x-gentelella::avatar name="Ada" status="online" />'))->toContain('<span class="online">');
});

it('renders a dismissible chip with an accessible remove label', function () {
    $html = render('<x-gentelella::chip tone="primary" active dismissible>Apparel</x-gentelella::chip>');

    expect($html)->toContain('class="chip chip-primary active"')
        ->toContain('class="chip-close"')
        ->toContain('Remove Apparel');
});

it('marks the active tab by index or by explicit flag', function () {
    expect(render('<x-gentelella::tabs :items="[\'One\', \'Two\']" :active="1" variant="pill" />'))
        ->toContain('class="tabs-pill"')
        ->toContain('<button class="tab" role="tab" aria-selected="false">One</button>')
        ->toContain('<button class="tab active" role="tab" aria-selected="true">Two</button>');

    expect(render('<x-gentelella::tabs :items="[[\'label\' => \'A\', \'active\' => true]]" />'))
        ->toContain('tab active');
});

it('renders table data attributes only when opted into', function () {
    expect(render('<x-gentelella::table><tbody></tbody></x-gentelella::table>'))
        ->toContain('<div class="table-responsive">')
        ->not->toContain('data-datatable');

    $html = render('<x-gentelella::table datatable :page-length="10" selectable export="customers"><tbody></tbody></x-gentelella::table>');

    expect($html)->toContain('data-datatable')
        ->toContain('data-page-length="10"')
        ->toContain('data-selectable')
        ->toContain('data-export="customers"');
});

it('can render a table without the scroll wrapper', function () {
    expect(render('<x-gentelella::table :responsive="false"><tbody></tbody></x-gentelella::table>'))
        ->not->toContain('table-responsive');
});

it('renders a toggle as a switch with its checked state', function () {
    expect(render('<x-gentelella::toggle on />'))
        ->toContain('role="switch"')
        ->toContain('aria-checked="true"')
        ->toContain('class="toggle on"');

    expect(render('<x-gentelella::toggle label="Email alerts" />'))
        ->toContain('<div class="toggle-row">')
        ->toContain('aria-checked="false"');
});

it('gives a warning banner an alert role and an info banner a status role', function () {
    expect(render('<x-gentelella::banner tone="warning">careful</x-gentelella::banner>'))
        ->toContain('class="banner banner-warning"')
        ->toContain('role="alert"');

    expect(render('<x-gentelella::banner>fyi</x-gentelella::banner>'))
        ->toContain('role="status"');
});

it('renders an accordion item as native details', function () {
    $html = render('<x-gentelella::accordion-item summary="What is included" open>Everything</x-gentelella::accordion-item>');

    expect($html)->toContain('<details')
        ->toContain('open="open"')
        ->toContain('class="accordion-item"')
        ->toContain('<summary class="accordion-summary">')
        ->toContain('<div class="accordion-content">Everything</div>');
});

it('renders a list group item as a link or a button, with meta', function () {
    expect(render('<x-gentelella::list-group-item active meta="14">Inbox</x-gentelella::list-group-item>'))
        ->toContain('<button type="button" class="list-group-item active"')
        ->toContain('<span class="meta">14</span>');

    expect(render('<x-gentelella::list-group-item href="/inbox">Inbox</x-gentelella::list-group-item>'))
        ->toContain('<a href="/inbox"');
});

it('renders the page header with a pretitle', function () {
    $html = render('<x-gentelella::page-header title="Tables" pretitle="Data" />');

    expect($html)->toContain('<div class="page-pretitle">Data</div>')
        ->toContain('<h1 class="page-title">Tables</h1>');
});

it('escapes content passed to components', function () {
    expect(render('<x-gentelella::status tone="red">{{ $x }}</x-gentelella::status>', ['x' => '<b>hi</b>']))
        ->toContain('&lt;b&gt;hi&lt;/b&gt;')
        ->not->toContain('<b>hi</b>');
});

it('renders remaining primitives', function () {
    expect(render('<x-gentelella::badge tone="red">3</x-gentelella::badge>'))->toContain('class="badge badge-red"')
        ->and(render('<x-gentelella::skeleton variant="circle" />'))->toContain('class="skeleton skeleton-circle"')
        ->and(render('<x-gentelella::spinner size="sm" tone="azure" />'))->toContain('class="spinner spinner-sm spinner-azure"')
        ->and(render('<x-gentelella::divider variant="dashed" />'))->toContain('class="divider-dashed"')
        ->and(render('<x-gentelella::divider label="or" />'))->toContain('<div class="divider-label"><span>or</span></div>')
        ->and(render('<x-gentelella::timeline-item title="Deployed" time="Just now" tone="green" description="in 28s" />'))
        ->toContain('class="timeline-item is-green"')
        ->and(render('<x-gentelella::empty-state title="No items yet" description="Create one." />'))
        ->toContain('<div class="empty-state-title">No items yet</div>');
});

it('fills every named slot when rendered from a real view', function () {
    // Named slots only compile correctly in a view file; Blade::render() on a
    // string leaks a literal @endslot and leaves the slot empty.
    $html = preg_replace('/\s+/', ' ', view('slots')->render()) ?? '';

    expect($html)
        ->toContain('<div class="card-options"><button class="card-opt-btn"')
        ->toContain('<div class="card-footer">FOOT</div>')
        ->toContain('<div class="page-actions"><button type="button" class="btn btn-primary">New</button></div>')
        ->toContain('<div class="stat-icon teal"><svg id="stat-icon">')
        ->toContain('<svg class="banner-icon">')
        ->toContain('<div class="banner-actions">')
        ->toContain('<div class="empty-state-icon"><svg id="empty-icon">');
});
