<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;

/** @return array<string, array{title: string, shell: bool, crud: bool}> */
function demoManifest(): array
{
    return require dirname(__DIR__, 2).'/resources/demo-pages.php';
}

/** Everything the document carries after the shell's </main>. */
function afterMain(string $html): string
{
    $at = strpos($html, '</main>');

    return $at === false ? '' : substr($html, $at);
}

/** @return list<string> */
function staticDemoSlugs(): array
{
    return array_keys(array_filter(demoManifest(), fn (array $m): bool => ! $m['crud']));
}

it('registers a route for every generated page', function () {
    foreach (staticDemoSlugs() as $slug) {
        expect(Route::has('gentelella.demo.'.$slug))
            ->toBeTrue("missing route for {$slug}");
    }
});

it('serves demo page', function (string $slug) {
    $this->get(route('gentelella.demo.'.$slug))->assertOk();
})->with(staticDemoSlugs());

it('wraps shell pages in the admin chrome and leaves bare pages bare', function () {
    $this->get(route('gentelella.demo.index'))
        ->assertOk()
        ->assertSee('<aside class="sidebar"', false)
        ->assertSee('id="main-content"', false)
        ->assertSee('<h1 class="page-title">Dashboard</h1>', false);

    $this->get(route('gentelella.demo.login'))
        ->assertOk()
        ->assertSee('class="auth-page"', false)
        ->assertDontSee('<aside class="sidebar"', false);
});

it('keeps literal at-directives out of blade compilation', function () {
    // The landing page prints "@use" as body text and Blade would otherwise
    // treat it as its own directive.
    $this->get(route('gentelella.demo.landing'))->assertOk()->assertSee('@use partials');

    // The two pages with a <style> block carry @media rules.
    $this->get(route('gentelella.demo.theme'))->assertOk()->assertSee('@media', false);
    $this->get(route('gentelella.demo.playground'))->assertOk()->assertSee('@media', false);
});

it('carries each page script through to the rendered page', function (string $slug) {
    // 25 of the pages put their behaviour in a <script> after </main>. Losing it
    // leaves a page that looks right and does nothing, which no status code
    // would catch. Look past </main>: the layout's own pre-paint script sits in
    // <head> and would satisfy a naive check on every page.
    $html = $this->get(route('gentelella.demo.'.$slug))->assertOk()->getContent();

    expect(afterMain($html))->toContain('<script');
})->with(array_keys(array_filter(
    demoManifest(),
    fn (array $m): bool => ! $m['crud'] && ($m['scripts'] ?? false),
)));

it('places page scripts after the content, not inside it', function () {
    $html = $this->get(route('gentelella.demo.settings'))->assertOk()->getContent();

    expect(afterMain($html))->toContain('<script')
        ->and(substr($html, 0, strpos($html, '</main>') ?: 0))
        ->not->toContain('id="settings-script-marker"');
});

it('keeps the style block that the theme generator needs', function () {
    $this->get(route('gentelella.demo.theme'))->assertOk()->assertSee('<style>', false);
});

it('marks the current page active in the sidebar', function () {
    $this->get(route('gentelella.demo.calendar'))
        ->assertOk()
        ->assertSee('aria-current="page"', false);
});

it('resolves menu links to demo routes rather than dead anchors', function () {
    $html = $this->get(route('gentelella.demo.index'))->assertOk()->getContent();

    expect($html)->toContain('href="'.url('/demo/form_advanced').'"')
        ->and($html)->toContain('href="'.url('/demo/inbox').'"');
});

it('redirects the demo root to the dashboard', function () {
    $this->get('/demo')->assertRedirect(route('gentelella.demo.index'));
});
