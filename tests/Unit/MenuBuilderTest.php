<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

it('falls back to the bundled demo menu when none is configured', function () {
    $menu = app(Gentelella::class)->menu();

    expect($menu->groups)->toHaveCount(7)
        ->and(collect($menu->groups)->pluck('label')->all())
        ->toBe(['General', 'Apps', 'E-commerce', 'Projects', 'UI library', 'Admin', 'Layouts']);
});

it('carries every item and submenu child through from the generated menu', function () {
    $items = collect(app(Gentelella::class)->menu()->groups)
        ->flatMap(fn (array $g) => $g['items'])
        ->flatMap(fn (array $i) => array_merge([$i], $i['children'] ?? []))
        ->count();

    // 7 groups / 51 nodes, matching `npm run export:php` in the upstream template.
    expect($items)->toBe(51);
});

it('marks the active leaf and opens its parent', function () {
    $menu = gentelellaWithMenu([[
        'label' => 'General',
        'items' => [[
            'text' => 'Forms',
            'icon' => 'forms',
            'children' => [
                ['key' => 'forms', 'text' => 'General', 'url' => '/forms'],
                ['key' => 'form-advanced', 'text' => 'Advanced', 'url' => '/forms/advanced'],
            ],
        ]],
    ]])->menu('form-advanced');

    $parent = $menu->groups[0]['items'][0];

    expect($parent['open'])->toBeTrue()
        ->and($parent['children'][0]['active'])->toBeFalse()
        ->and($parent['children'][1]['active'])->toBeTrue();
});

it('leaves a parent closed when no child matches', function () {
    $menu = gentelellaWithMenu([[
        'label' => 'General',
        'items' => [[
            'text' => 'Forms',
            'children' => [['key' => 'forms', 'text' => 'General', 'url' => '/forms']],
        ]],
    ]])->menu('something-else');

    expect($menu->groups[0]['items'][0]['open'])->toBeFalse();
});

it('drops items the user is not authorised to see', function () {
    Gate::define('manage-users', fn (?User $user) => false);
    Gate::define('view-reports', fn (?User $user) => true);

    $menu = gentelellaWithMenu([[
        'label' => 'Admin',
        'items' => [
            ['key' => 'users', 'text' => 'Users', 'url' => '/users', 'can' => 'manage-users'],
            ['key' => 'reports', 'text' => 'Reports', 'url' => '/reports', 'can' => 'view-reports'],
            ['key' => 'open', 'text' => 'Open', 'url' => '/open'],
        ],
    ]])->menu();

    expect(collect($menu->groups[0]['items'])->pluck('text')->all())
        ->toBe(['Reports', 'Open']);
});

it('drops a parent once every child has been gated away', function () {
    Gate::define('nope', fn (?User $user) => false);

    $menu = gentelellaWithMenu([[
        'label' => 'Admin',
        'items' => [[
            'text' => 'Restricted',
            'children' => [['key' => 'a', 'text' => 'A', 'url' => '/a', 'can' => 'nope']],
        ]],
    ]])->menu();

    expect($menu->groups)->toBe([]);
});

it('resolves hrefs from url, route name, and demo page slug', function () {
    Route::get('/reports', fn () => '')->name('admin.reports');

    $menu = gentelellaWithMenu([[
        'label' => 'General',
        'items' => [
            ['key' => 'a', 'text' => 'Explicit', 'url' => '/verbatim'],
            ['key' => 'b', 'text' => 'Named', 'route' => 'admin.reports'],
            ['key' => 'c', 'text' => 'Missing', 'route' => 'route.that.does.not.exist'],
            ['key' => 'd', 'text' => 'Demo', 'page' => 'index'],
        ],
    ]])->menu();

    expect(collect($menu->groups[0]['items'])->pluck('href')->all())
        ->toBe(['/verbatim', url('/reports'), '#', '#']);
});

it('collects reachable leaves for the command palette and skips dead links', function () {
    $menu = gentelellaWithMenu([[
        'label' => 'General',
        'items' => [
            ['key' => 'a', 'text' => 'Reachable', 'url' => '/a'],
            ['key' => 'b', 'text' => 'Dead', 'route' => 'nope'],
            ['text' => 'Parent', 'children' => [['key' => 'c', 'text' => 'Child', 'url' => '/c']]],
        ],
    ]])->menu();

    // Sidebar reading order: a leaf listed before a later parent's child.
    expect($menu->searchable)->toBe([
        ['text' => 'Reachable', 'href' => '/a', 'group' => 'General'],
        ['text' => 'Child', 'href' => '/c', 'group' => 'General'],
    ]);
});
