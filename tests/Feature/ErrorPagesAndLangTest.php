<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('ships an error view for each common status code', function (string $code) {
    expect(view()->exists('gentelella::errors.'.$code))->toBeTrue();
})->with(['403', '404', '419', '429', '500', '503']);

it('renders an error page on the template markup', function () {
    $html = view('gentelella::errors.404', ['exception' => null])->render();

    expect($html)->toContain('class="error-page"')
        ->toContain('<div class="error-code">404</div>')
        ->toContain('Page not found')
        ->toContain('Back to dashboard')
        ->not->toContain('<aside class="sidebar"');
});

it('shows a deliberate abort message on a 4xx', function () {
    // abort(403, 'Only owners may do that') is wording someone chose.
    $html = view('gentelella::errors.403', ['exception' => new HttpException(403, 'Only owners may do that')])->render();

    expect($html)->toContain('Only owners may do that');
});

it('never echoes the exception message on a 5xx', function () {
    // The message there is the raw exception — internals, not user-facing text.
    $html = view('gentelella::errors.500', [
        'exception' => new HttpException(500, 'SQLSTATE[42S02]: Base table or view not found: users'),
    ])->render();

    expect($html)->not->toContain('SQLSTATE')
        ->not->toContain('users')
        ->toContain('An unexpected error occurred');
});

it('does not leak framework wording on 419, 429 or 503', function (string $code) {
    $html = view('gentelella::errors.'.$code, ['exception' => new HttpException((int) $code, 'internal detail')])->render();

    expect($html)->not->toContain('internal detail');
})->with(['419', '429', '503']);

it('publishes the error views into the application', function () {
    // Laravel's handler replaces the `errors` namespace with the application's
    // own view paths, so a package cannot register these — they have to land in
    // resources/views/errors to be used.
    $this->artisan('vendor:publish', ['--tag' => 'gentelella-errors', '--force' => true])->assertSuccessful();

    expect(File::exists(resource_path('views/errors/404.blade.php')))->toBeTrue()
        ->and(File::get(resource_path('views/errors/404.blade.php')))->toContain('gentelella::errors.layout');

    File::deleteDirectory(resource_path('views/errors'));
});

it('ships a translation catalogue covering the strings it uses', function () {
    $catalogue = json_decode((string) file_get_contents(dirname(__DIR__, 2).'/resources/lang/en.json'), true);

    expect($catalogue)->toBeArray()->not->toBeEmpty()
        ->and($catalogue)->toHaveKeys(['Save', 'Cancel', 'Sign in', 'Back to dashboard']);
});

it('lets an application translate the package strings', function () {
    // JSON translations, so an app overrides with lang/<locale>.json and never
    // has to publish a view.
    $dir = sys_get_temp_dir().'/gentelella-lang-'.uniqid();
    File::ensureDirectoryExists($dir);
    File::put($dir.'/fr.json', json_encode(['Save' => 'Enregistrer']));

    app('translator')->addJsonPath($dir);
    app()->setLocale('fr');

    expect(__('Save'))->toBe('Enregistrer')
        // Untranslated strings still fall through to the English key.
        ->and(__('Cancel'))->toBe('Cancel');

    File::deleteDirectory($dir);
});

it('owns its auth wording rather than relying on published lang files', function () {
    // Laravel's lang/en/auth.php is not published by default; leaning on
    // auth.failed would render the raw key.
    expect(__('These credentials do not match our records.'))
        ->toBe('These credentials do not match our records.')
        ->not->toContain('auth.');
});
