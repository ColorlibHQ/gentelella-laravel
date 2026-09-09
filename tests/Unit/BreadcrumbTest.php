<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;

/** @return array<int, array{label: string, items: array<int, array<string, mixed>>}> */
function breadcrumbMenu(): array
{
    return [[
        'label' => 'General',
        'items' => [
            ['key' => 'dashboard', 'text' => 'Dashboard', 'url' => '/admin'],
            [
                'text' => 'Forms',
                'children' => [['key' => 'forms', 'text' => 'Forms', 'url' => '/admin/forms']],
            ],
        ],
    ]];
}

it('splits on the > separator and marks the last crumb current', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('Home > Forms > Advanced');

    expect($crumbs)->toHaveCount(3)
        ->and($crumbs[2])->toBe(['text' => 'Advanced', 'href' => null, 'current' => true])
        ->and($crumbs[0]['current'])->toBeFalse();
});

it('resolves an intermediate crumb by exact label match against the menu', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('Home > Forms > Advanced');

    expect($crumbs[1])->toBe(['text' => 'Forms', 'href' => '/admin/forms', 'current' => false]);
});

it('prefers an explicit target after the pipe', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())
        ->breadcrumb('Home > Projects|/projects/acme > Acme Redesign');

    expect($crumbs[1])->toBe([
        'text' => 'Projects',
        'href' => '/projects/acme',
        'current' => false,
    ]);
});

it('leaves an unresolvable crumb as plain text', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('Home > Nowhere > Leaf');

    expect($crumbs[1]['href'])->toBeNull();
});

it('seeds Home from the first menu item', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('Home > Anything');

    expect($crumbs[0]['href'])->toBe('/admin');
});

it('never links the last crumb even when the label matches the menu', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('Home > Forms');

    expect($crumbs[1])->toBe(['text' => 'Forms', 'href' => null, 'current' => true]);
});

it('falls back to a single Home crumb for an empty breadcrumb', function () {
    expect(gentelellaWithMenu(breadcrumbMenu())->breadcrumb('   '))
        ->toHaveCount(1)
        ->and(gentelellaWithMenu(breadcrumbMenu())->breadcrumb('')[0]['text'])->toBe('Home');
});

it('tolerates stray separators and whitespace', function () {
    $crumbs = gentelellaWithMenu(breadcrumbMenu())->breadcrumb('  Home  >>  Forms  >  Advanced  ');

    expect(collect($crumbs)->pluck('text')->all())->toBe(['Home', 'Forms', 'Advanced']);
});

it('builds the page title from the configured base', function () {
    $gentelella = app(Gentelella::class);

    expect($gentelella->title('Dashboard'))->toBe('Dashboard | Gentelella')
        ->and($gentelella->title())->toBe('Gentelella')
        ->and($gentelella->title(''))->toBe('Gentelella');
});
