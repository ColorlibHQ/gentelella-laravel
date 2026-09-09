<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Documentation drifts silently: nothing breaks when a doc names a command that
 * was renamed or a config key that was dropped. These tests read the docs and
 * check their claims against the code.
 */
function packageRoot(): string
{
    return dirname(__DIR__, 2);
}

/** @return list<string> */
function docFiles(): array
{
    return array_map(
        fn ($f): string => $f->getPathname(),
        File::files(packageRoot().'/docs'),
    );
}

/** The first backticked token in each table row, which is where these docs put the name. */
function tableKeys(string $markdown): array
{
    preg_match_all('/^\|\s*`([^`]+)`/m', $markdown, $m);

    return $m[1];
}

it('has no broken internal links', function () {
    $broken = [];

    foreach (array_merge(docFiles(), [packageRoot().'/README.md']) as $file) {
        preg_match_all('/\]\(([^)#:]+\.md)(#[^)]*)?\)/', (string) File::get($file), $m);

        foreach ($m[1] as $target) {
            $resolved = realpath(dirname($file).'/'.$target);

            if ($resolved === false) {
                $broken[] = basename($file).' -> '.$target;
            }
        }
    }

    expect($broken)->toBe([]);
});

it('only names artisan commands that exist', function () {
    $registered = array_keys(Artisan::all());
    $named = [];

    foreach (docFiles() as $file) {
        preg_match_all('/gentelella:[a-z-]+/', (string) File::get($file), $m);
        $named = array_merge($named, $m[0]);
    }

    $named = array_values(array_unique($named));

    expect($named)->not->toBeEmpty();

    expect(array_values(array_diff($named, $registered)))->toBe([]);
});

it('documents every registered command', function () {
    $documented = (string) File::get(packageRoot().'/docs/commands.md');

    $undocumented = array_values(array_filter(
        array_keys(Artisan::all()),
        fn (string $c): bool => str_starts_with($c, 'gentelella:') && ! str_contains($documented, $c),
    ));

    expect($undocumented)->toBe([]);
});

it('only documents config keys that exist', function () {
    $config = require packageRoot().'/config/gentelella.php';
    $missing = [];

    foreach (tableKeys((string) File::get(packageRoot().'/docs/configuration.md')) as $key) {
        foreach (preg_split('#\s*/\s*#', $key) ?: [] as $candidate) {
            $candidate = trim($candidate);

            if ($candidate === '' || str_contains($candidate, ' ')) {
                continue;
            }

            if (! Arr::has($config, $candidate)) {
                $missing[] = $candidate;
            }
        }
    }

    expect($missing)->toBe([]);
});

it('documents every column type that ships', function () {
    $documented = tableKeys((string) File::get(packageRoot().'/docs/columns.md'));

    $undocumented = [];

    foreach (File::files(packageRoot().'/resources/views/columns') as $view) {
        $type = Str::before($view->getFilename(), '.blade.php');

        if (! in_array($type, $documented, true)) {
            $undocumented[] = $type;
        }
    }

    expect($undocumented)->toBe([]);
});

it('documents every field type that ships', function () {
    $markdown = (string) File::get(packageRoot().'/docs/fields.md');
    $documented = [];

    // Field rows list several types per cell: `email`, `tel`, `url`, `password`
    foreach (tableKeys($markdown) as $cell) {
        foreach (explode(',', $cell) as $type) {
            $documented[] = trim(str_replace('`', '', $type));
        }
    }

    // The row's remaining backticked names are not captured by tableKeys().
    preg_match_all('/^\|\s*(`[^|]+`)\s*\|/m', $markdown, $m);
    foreach ($m[1] as $cell) {
        preg_match_all('/`([a-z_]+)`/', $cell, $names);
        $documented = array_merge($documented, $names[1]);
    }

    $undocumented = [];

    foreach (File::files(packageRoot().'/resources/views/fields') as $view) {
        $type = Str::before($view->getFilename(), '.blade.php');

        if (! in_array($type, $documented, true)) {
            $undocumented[] = $type;
        }
    }

    expect($undocumented)->toBe([]);
});

it('documents every public component', function () {
    $documented = (string) File::get(packageRoot().'/docs/components.md');
    $dir = packageRoot().'/resources/views/components';

    $names = array_map(
        fn ($f): string => Str::before($f->getFilename(), '.blade.php'),
        File::files($dir),
    );

    $undocumented = array_values(array_filter(
        $names,
        fn (string $n): bool => ! str_contains($documented, '`'.$n.'`'),
    ));

    expect($undocumented)->toBe([]);
});

it('keeps the readme roadmap in step with what ships', function () {
    $readme = (string) File::get(packageRoot().'/README.md');

    // Anything ticked has to be real.
    expect($readme)->toContain('- [x] Menu layer')
        ->and($readme)->toContain('- [x] Component library')
        ->and($readme)->toContain('- [x] CRUD engine')
        ->and($readme)->toContain('- [x] Demo application')
        ->and($readme)->toContain('- [x] Auth');
});
