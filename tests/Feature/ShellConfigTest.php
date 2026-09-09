<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;
use Illuminate\Support\Facades\Route;

/** The parsed contents of the config island on a rendered shell page. */
function islandFrom(string $html): array
{
    preg_match('#<script type="application/json" id="gentelella-shell-config">(.*?)</script>#s', $html, $m);

    return json_decode(html_entity_decode($m[1] ?? '{}', ENT_QUOTES), true) ?? [];
}

it('emits the config island on shell pages', function () {
    $island = islandFrom(renderShell());

    expect($island)->toHaveKeys(['links', 'pages']);
});

it('does not emit it on bare pages', function () {
    // The palette only exists inside the shell.
    expect(view('bare')->render())->not->toContain('gentelella-shell-config');
});

it('hands over the application menu, not the template demo pages', function () {
    gentelellaWithMenu([[
        'label' => 'Catalogue',
        'items' => [
            ['key' => 'products', 'text' => 'Products', 'url' => '/admin/products'],
            ['text' => 'Stock', 'children' => [['key' => 'levels', 'text' => 'Levels', 'url' => '/admin/stock']]],
        ],
    ]]);

    $pages = islandFrom(renderShell('products'))['pages'];

    expect($pages)->toBe([
        ['label' => 'Products', 'section' => 'Catalogue', 'href' => '/admin/products'],
        ['label' => 'Levels', 'section' => 'Catalogue', 'href' => '/admin/stock'],
    ]);
});

it('omits menu entries that resolve nowhere', function () {
    gentelellaWithMenu([[
        'label' => 'General',
        'items' => [
            ['key' => 'ok', 'text' => 'Reachable', 'url' => '/ok'],
            ['key' => 'dead', 'text' => 'Dead', 'route' => 'route.that.does.not.exist'],
        ],
    ]]);

    expect(islandFrom(renderShell())['pages'])
        ->toBe([['label' => 'Reachable', 'section' => 'General', 'href' => '/ok']]);
});

it('resolves links from route names and absolute paths', function () {
    Route::get('/account', fn () => '')->name('account.show');

    config()->set('gentelella.links', [
        'profile' => 'account.show',
        'settings' => '/settings',
        'help' => 'https://example.com/help',
        'theme' => 'no.such.route',
        'lock' => null,
    ]);
    app()->forgetInstance(Gentelella::class);

    $links = islandFrom(renderShell())['links'];

    expect($links)->toBe([
        'profile' => url('/account'),
        'settings' => '/settings',
        'help' => 'https://example.com/help',
    ]);
});

it('publishes the logout url when the route exists', function () {
    Route::post('/logout', fn () => '')->name('logout');
    app()->forgetInstance(Gentelella::class);

    // Sign-out has to POST, so the design system needs the URL and the CSRF
    // token — both of which come from the page.
    $html = renderShell();

    expect(islandFrom($html)['links']['logout'])->toBe(url('/logout'))
        ->and($html)->toContain('name="csrf-token"');
});

it('leaves logout out when no such route exists', function () {
    expect(islandFrom(renderShell())['links'])->not->toHaveKey('logout');
});
