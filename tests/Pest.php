<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;
use ColorlibHQ\Gentelella\Tests\AuthTestCase;
use ColorlibHQ\Gentelella\Tests\DemoTestCase;
use ColorlibHQ\Gentelella\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

uses(TestCase::class)->in('Unit', 'Feature');
uses(DemoTestCase::class)->in('Demo');
uses(AuthTestCase::class)->in('Auth');

/**
 * Render a Blade snippet and collapse formatting whitespace.
 *
 * Blade indents component output; assertions should be about structure, not
 * about where the compiler put its newlines.
 *
 * @param  array<string, mixed>  $data
 */
function render(string $template, array $data = []): string
{
    $html = preg_replace('/\s+/', ' ', Blade::render($template, $data)) ?? '';

    return trim(str_replace(['> ', ' <', ' >'], ['>', '<', '>'], $html));
}

/**
 * Render a page that extends the admin shell.
 *
 * Uses a real fixture view rather than Blade::render(): the latter leaks one
 * output buffer level per @extends, which PHPUnit flags as a risky test.
 */
function renderShell(string $pageKey = 'dashboard', string $breadcrumb = 'Home > Dashboard'): string
{
    app()->forgetInstance(Gentelella::class);

    return view('shell', ['pageKey' => $pageKey, 'breadcrumb' => $breadcrumb])->render();
}

/**
 * Replace the configured menu and hand back a freshly resolved Gentelella.
 *
 * The service memoises its menu, so tests that change the definition need a new
 * instance rather than the one the container already built.
 *
 * @param  array<int, array{label: string, items: array<int, array<string, mixed>>}>  $menu
 */
function gentelellaWithMenu(array $menu): Gentelella
{
    config()->set('gentelella.menu', $menu);
    app()->forgetInstance(Gentelella::class);

    return app(Gentelella::class);
}
