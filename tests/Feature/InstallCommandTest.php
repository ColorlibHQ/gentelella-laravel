<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/** @return list<string> */
function stubPaths(): array
{
    return [
        app()->basePath('resources/js/gentelella.js'),
        app()->basePath('resources/css/gentelella.scss'),
    ];
}

afterEach(function () {
    foreach (stubPaths() as $path) {
        File::delete($path);
    }
    File::delete(config_path('gentelella.php'));
});

it('creates both vite entry stubs', function () {
    $this->artisan('gentelella:install')->assertSuccessful();

    [$js, $scss] = stubPaths();

    expect(File::exists($js))->toBeTrue()
        ->and(File::exists($scss))->toBeTrue()
        ->and(File::get($js))->toContain("import 'gentelella';")
        ->and(File::get($js))->toContain("import '../css/gentelella.scss';");
});

it('leaves an existing stub alone unless forced', function () {
    [$js] = stubPaths();
    File::ensureDirectoryExists(dirname($js));
    File::put($js, '// mine');

    $this->artisan('gentelella:install --only=stubs')
        ->expectsOutputToContain('already exists')
        ->assertSuccessful();

    expect(File::get($js))->toBe('// mine');

    $this->artisan('gentelella:install --only=stubs --force')->assertSuccessful();

    expect(File::get($js))->toContain("import 'gentelella';");
});

it('writes no stubs when limited to the config step', function () {
    $this->artisan('gentelella:install --only=config')->assertSuccessful();

    foreach (stubPaths() as $path) {
        expect(File::exists($path))->toBeFalse();
    }
});

it('rejects an unknown --only value', function () {
    $this->artisan('gentelella:install --only=nope')
        ->expectsOutputToContain('Unknown --only value')
        ->assertFailed();
});

it('publishes the config file', function () {
    $this->artisan('gentelella:install --only=config')->assertSuccessful();

    expect(File::exists(config_path('gentelella.php')))->toBeTrue();
});
