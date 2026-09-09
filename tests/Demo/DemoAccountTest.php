<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
 * These need the demo migrations, which are only registered when demo mode is
 * on as the application boots — so they live in the Demo suite rather than the
 * Auth one.
 */

it('creates an account those credentials actually sign in with', function () {
    $demo = config('gentelella.demo_user');

    $this->artisan('gentelella:demo')->assertSuccessful();

    $user = User::where('email', $demo['email'])->firstOrFail();

    expect($user->name)->toBe($demo['name'])
        ->and(Hash::check($demo['password'], $user->password))->toBeTrue();

    $this->post('/login', ['email' => $demo['email'], 'password' => $demo['password']])
        ->assertRedirect();

    expect(Auth::check())->toBeTrue();
});

it('resets the demo password when run again', function () {
    $demo = config('gentelella.demo_user');
    $this->artisan('gentelella:demo')->assertSuccessful();

    // Somebody changed it on the public demo.
    User::where('email', $demo['email'])->update(['password' => Hash::make('tampered')]);

    $this->artisan('gentelella:demo')->assertSuccessful();

    expect(Hash::check($demo['password'], User::where('email', $demo['email'])->first()->password))->toBeTrue()
        ->and(User::where('email', $demo['email'])->count())->toBe(1);
});

it('skips the account rather than failing when there is no users table', function () {
    Schema::drop('users');

    $this->artisan('gentelella:demo')
        ->expectsOutputToContain('skipped the demo account')
        ->assertSuccessful();
});
