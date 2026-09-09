<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Auth;

use ColorlibHQ\Gentelella\Auth\Http\Controllers\LoginController;
use ColorlibHQ\Gentelella\Auth\Http\Controllers\PasswordResetController;
use ColorlibHQ\Gentelella\Auth\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/**
 * Registers the auth screens under Laravel's conventional route names.
 *
 * The names matter: `login` is where the framework's own auth middleware sends
 * an unauthenticated visitor, and `password.request` / `password.store` are
 * what the password broker's notification links expect. Using anything else
 * would make these screens work only when reached by hand.
 *
 * Static and stateless — see Crud\RouteRegistrar for why.
 */
class AuthRouteRegistrar
{
    public static function register(): void
    {
        /** @var array<string, mixed> $config */
        $config = (array) config('gentelella.auth', []);

        $prefix = trim((string) ($config['prefix'] ?? ''), '/');
        $middleware = $config['middleware'] ?? ['web'];

        Route::middleware($middleware)->prefix($prefix)->group(function () use ($config): void {
            Route::middleware('guest')->group(function () use ($config): void {
                if (! Route::has('login')) {
                    Route::get('login', [LoginController::class, 'create'])->name('login');
                    Route::post('login', [LoginController::class, 'store']);
                }

                if (($config['register'] ?? true) && ! Route::has('register')) {
                    Route::get('register', [RegisterController::class, 'create'])->name('register');
                    Route::post('register', [RegisterController::class, 'store']);
                }

                if (($config['reset'] ?? true) && ! Route::has('password.request')) {
                    Route::get('forgot-password', [PasswordResetController::class, 'request'])
                        ->name('password.request');
                    Route::post('forgot-password', [PasswordResetController::class, 'email'])
                        ->name('password.email');
                    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])
                        ->name('password.reset');
                    Route::post('reset-password', [PasswordResetController::class, 'update'])
                        ->name('password.store');
                }
            });

            if (! Route::has('logout')) {
                Route::post('logout', [LoginController::class, 'destroy'])
                    ->middleware('auth')
                    ->name('logout');
            }
        });
    }
}
