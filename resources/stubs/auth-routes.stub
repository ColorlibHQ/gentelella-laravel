<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
 * Gentelella auth routes.
 *
 * The names are Laravel's conventional ones on purpose: `login` is where the
 * framework's auth middleware sends an unauthenticated visitor, and the
 * password broker's emailed link points at `password.reset`.
 *
 * Require this file from routes/web.php (or bootstrap/app.php), and turn the
 * package's own copy off with 'auth' => ['enabled' => false] in
 * config/gentelella.php so the two do not both register.
 */

Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
