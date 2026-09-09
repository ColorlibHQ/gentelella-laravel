<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;

it('exposes the generated icon set', function () {
    expect(app(Gentelella::class)->icons())->toHaveCount(29);
});

it('returns well-formed inline svg for a known icon', function () {
    $svg = app(Gentelella::class)->icon('dashboard');

    expect($svg)->toStartWith('<svg')
        ->and($svg)->toEndWith('</svg>')
        ->and($svg)->toContain('stroke="currentColor"');
});

it('returns an empty string for an unknown or absent icon', function () {
    expect(app(Gentelella::class)->icon('does-not-exist'))->toBe('')
        ->and(app(Gentelella::class)->icon(null))->toBe('');
});

it('resolves every icon referenced by the bundled menu', function () {
    $gentelella = app(Gentelella::class);

    $referenced = collect($gentelella->menu()->groups)
        ->flatMap(fn (array $g) => $g['items'])
        ->pluck('icon')
        ->filter()
        ->unique();

    expect($referenced)->not->toBeEmpty();

    foreach ($referenced as $name) {
        expect($gentelella->icon($name))->not->toBe('', "icon '{$name}' is missing");
    }
});
