<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;
use Illuminate\Foundation\Vite;

/*
 * The demo pages import `/src/v4/toast.js` and `leaflet` from inline module
 * scripts. Neither resolves in a Laravel app, so the layout writes an import map
 * to the files Vite built. The suite has no Vite manifest, so a stand-in answers
 * for the entries a real build would have produced.
 */
function fakeVite(array $built): void
{
    app()->instance(Vite::class, new class($built) extends Vite
    {
        public function __construct(private array $built) {}

        public function asset($asset, $buildDirectory = null)
        {
            return $this->built[$asset] ?? throw new RuntimeException("Unable to locate file in Vite manifest: {$asset}.");
        }

        public function __invoke($entrypoints, $buildDirectory = null)
        {
            return '';
        }
    });
}

it('maps each demo import to the file Vite built', function () {
    fakeVite([
        'node_modules/gentelella/src/v4/toast.js' => 'https://app.test/build/assets/toast-abc.js',
        'node_modules/leaflet/dist/leaflet-src.js' => 'https://app.test/build/assets/leaflet-src-def.js',
    ]);

    $map = app(Gentelella::class)->demoImportMap();

    expect($map)->toBe([
        '/src/v4/toast.js' => 'https://app.test/build/assets/toast-abc.js',
        'leaflet' => 'https://app.test/build/assets/leaflet-src-def.js',
    ]);
});

it('skips a module the app did not build instead of failing the page', function () {
    fakeVite([]);

    expect(app(Gentelella::class)->demoImportMap())->toBe([]);
    $this->get('/demo/index')->assertOk()->assertDontSee('importmap', false);
});

it('writes the import map before any module script', function () {
    fakeVite(['node_modules/gentelella/src/v4/toast.js' => 'https://app.test/build/assets/toast-abc.js']);

    $html = $this->get('/demo/index')->assertOk()->getContent();

    expect($html)->toContain('<script type="importmap">{"imports":{"/src/v4/toast.js":"https://app.test/build/assets/toast-abc.js"}}</script>');
});

it('loads the Leaflet stylesheet on the map page only', function () {
    fakeVite(['node_modules/leaflet/dist/leaflet.css' => 'https://app.test/build/assets/leaflet-xyz.css']);

    $this->get('/demo/map')->assertOk()->assertSee('leaflet-xyz.css', false);
    $this->get('/demo/index')->assertOk()->assertDontSee('leaflet-xyz.css', false);
});

it('emits nothing when demo mode is off', function () {
    config(['gentelella.demo' => false]);
    fakeVite(['node_modules/gentelella/src/v4/toast.js' => 'https://app.test/build/assets/toast-abc.js']);

    expect(app(Gentelella::class)->demoImportMap())->toBe([])
        ->and(app(Gentelella::class)->demoStylesheets('map'))->toBe([]);
});
