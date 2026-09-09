<?php

declare(strict_types=1);
use ColorlibHQ\Gentelella\Gentelella;

it('renders the shell in the same DOM order as the static template', function () {
    $html = renderShell();

    $skip = strpos($html, 'class="skip-link"');
    $aside = strpos($html, '<aside class="sidebar"');
    $topbar = strpos($html, '<header class="topbar">');
    $main = strpos($html, 'id="main-content"');
    $footer = strpos($html, '<footer class="footer">');
    $mainEnd = strpos($html, '</main>');

    expect($skip)->toBeLessThan($aside)
        ->and($aside)->toBeLessThan($topbar)
        ->and($topbar)->toBeLessThan($main)
        ->and($main)->toBeLessThan($footer)
        ->and($footer)->toBeLessThan($mainEnd);
});

it('puts the page content inside the page wrapper', function () {
    expect(renderShell())->toContain('<div class="page-wrapper">')
        ->toContain('<p id="probe">page body</p>');
});

it('emits the pre-paint theme script before the body', function () {
    $html = renderShell();

    expect($html)->toContain("localStorage.getItem('theme')")
        ->and(strpos($html, "localStorage.getItem('theme')"))->toBeLessThan(strpos($html, '<body'));
});

it('composes the page title from the section and the configured base', function () {
    expect(renderShell())->toContain('<title>Dashboard | Gentelella</title>');
});

it('marks the active leaf and opens its parent', function () {
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [[
            'text' => 'Forms',
            'icon' => 'forms',
            'children' => [
                ['key' => 'forms', 'text' => 'General', 'url' => '/forms'],
                ['key' => 'form-advanced', 'text' => 'Advanced', 'url' => '/forms/advanced'],
            ],
        ]],
    ]]);

    $html = renderShell('form-advanced', 'Home > Forms > Advanced');

    expect($html)->toContain('class="nav-tree open has-active"')
        ->toContain('aria-expanded="true"')
        ->toContain('<a class="nav-sublink active"')
        ->toContain('aria-current="page"');
});

it('renders a leaf without the active class when it is not the current page', function () {
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [['key' => 'other', 'text' => 'Other', 'icon' => 'pages', 'url' => '/other']],
    ]]);

    expect(renderShell('dashboard'))->toContain('<a class="nav-link"')
        ->not->toContain('class="nav-link active"');
});

it('renders breadcrumbs with separators and a non-linked current crumb', function () {
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [['key' => 'forms', 'text' => 'Forms', 'url' => '/forms']],
    ]]);

    $html = renderShell('dashboard', 'Home > Forms > Advanced');

    expect($html)->toContain('<a href="/forms">Forms</a>')
        ->toContain('<span class="sep" aria-hidden="true">›</span>')
        ->toContain('aria-current="page" >Advanced</span>');
});

it('renders icon svg raw but escapes item text', function () {
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [['key' => 'x', 'text' => 'Reports & <b>More</b>', 'icon' => 'charts', 'url' => '/x']],
    ]]);

    $html = renderShell('x');

    expect($html)->toContain('<svg class="icon"')
        ->toContain('Reports &amp; &lt;b&gt;More&lt;/b&gt;')
        ->not->toContain('<b>More</b>');
});

it('renders a badge when the item defines one', function () {
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [[
            'key' => 'x', 'text' => 'Forms', 'icon' => 'forms', 'url' => '/x',
            'badge' => ['text' => 'Hot', 'class' => 'badge-red'],
        ]],
    ]]);

    expect(renderShell('x'))->toContain('<span class="badge badge-red">Hot</span>');
});

it('omits topbar affordances that are switched off', function () {
    config()->set('gentelella.search_enabled', false);
    config()->set('gentelella.theme_toggle', false);
    config()->set('gentelella.notifications_enabled', false);
    config()->set('gentelella.messages_enabled', false);
    config()->set('gentelella.docs_url', null);

    $html = renderShell();

    expect($html)->not->toContain('class="search-box"')
        ->not->toContain('theme-toggle')
        ->not->toContain('tb-notifications')
        ->not->toContain('tb-messages')
        ->not->toContain('tb-docs');
});

it('renders the blank layout without any shell chrome', function () {
    $html = view('bare')->render();

    expect($html)->toContain('<form id="login"></form>')
        ->toContain('<title>Login | Gentelella</title>')
        ->not->toContain('<aside class="sidebar"')
        ->not->toContain('<header class="topbar">');
});

it('omits optional document assets until they are configured', function () {
    expect(renderShell())->not->toContain('rel="manifest"')
        ->not->toContain('apple-touch-icon');

    config()->set('gentelella.manifest', 'site.webmanifest');
    config()->set('gentelella.apple_touch_icon', 'images/apple-touch-icon.svg');

    expect(renderShell())->toContain('rel="manifest"')
        ->toContain('apple-touch-icon');
});

it('survives blade escaping the inline section value', function () {
    // Blade compiles @section('x', 'literal') through e(), so the breadcrumb
    // separator arrives as &gt; and the title's ampersand as &amp;.
    config()->set('gentelella.menu', [[
        'label' => 'General',
        'items' => [['key' => 'rd', 'text' => 'R&D', 'url' => '/rd']],
    ]]);

    app()->forgetInstance(Gentelella::class);
    $html = view('sections')->render();

    expect($html)->toContain('<title>Q1 &amp; Q2 | Gentelella</title>')
        ->toContain('<a href="/rd">R&amp;D</a>')
        ->toContain('>Roadmap</span>')
        ->not->toContain('&amp;gt;');
});

it('opts out of the service worker unless one was published', function () {
    // The design system registers /sw.js in production builds. A Laravel app
    // has no such file, so every page would log a 404 for it.
    expect(renderShell())->toContain('data-sw="off"');

    config()->set('gentelella.service_worker', true);

    expect(renderShell())->not->toContain('data-sw="off"');
});

it('marks the body as a shell page so the design system wires itself', function () {
    // mountShell() returns immediately unless body[data-shell="admin"] is
    // present. Without it the sidebar accordion, the mobile drawer, the theme
    // toggle and every topbar panel are inert — markup that looks right and
    // does nothing.
    expect(renderShell())->toContain('data-shell="admin"');
});

it('leaves the marker off bare pages, which have no shell to wire', function () {
    expect(view('bare')->render())->not->toContain('data-shell');
});

it('points the topbar docs button at this edition, not the html one', function () {
    // A Blade developer sent to the static template's docs finds nothing about
    // panels, fields or filters.
    expect(config('gentelella.docs_url'))->toContain('/docs/laravel')
        ->and(renderShell())->toContain('href="'.config('gentelella.docs_url').'"');
});
