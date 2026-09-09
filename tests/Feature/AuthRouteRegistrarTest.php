<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Auth\AuthRouteRegistrar;
use Illuminate\Support\Facades\Route;

/**
 * Register the auth routes and rebuild the name lookup.
 *
 * Routes added after the application has booted are absent from the by-name
 * index until it is refreshed. Real registration happens during boot, so this
 * is a test concern only.
 */
function registerAuthRoutes(): void
{
    AuthRouteRegistrar::register();
    Route::getRoutes()->refreshNameLookups();
}

it('does not register auth routes by default', function () {
    expect(config('gentelella.auth.enabled'))->toBeFalse()
        ->and(Route::has('login'))->toBeFalse();
});

it('leaves an application route of the same name alone', function () {
    // An app on a starter kit already owns `login`; the package must not shadow
    // it, or a customised sign-in silently stops being reachable.
    Route::get('login', fn () => 'app login')->middleware('web')->name('login');
    Route::getRoutes()->refreshNameLookups();

    registerAuthRoutes();

    // The app's own screen is what answers, not the package's.
    $this->get('/login')->assertOk()->assertSee('app login');
});

it('skips only the screens that already exist', function () {
    Route::get('login', fn () => 'app login')->name('login');
    Route::getRoutes()->refreshNameLookups();

    registerAuthRoutes();

    // Registration and reset are untouched by the app, so they are registered.
    expect(Route::has('register'))->toBeTrue()
        ->and(Route::has('password.request'))->toBeTrue();
});

it('honours the register and reset switches', function () {
    config()->set('gentelella.auth.register', false);
    config()->set('gentelella.auth.reset', false);

    registerAuthRoutes();

    expect(Route::has('login'))->toBeTrue()
        ->and(Route::has('register'))->toBeFalse()
        ->and(Route::has('password.request'))->toBeFalse();
});

it('applies the configured prefix', function () {
    config()->set('gentelella.auth.prefix', 'account');

    registerAuthRoutes();

    expect(Route::getRoutes()->getByName('login')->uri())->toBe('account/login');
});
