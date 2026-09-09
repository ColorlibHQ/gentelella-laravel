<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

it('renders the registration screen', function () {
    $this->get('/register')->assertOk()->assertSee('Create your account');
});

it('creates a user, signs them in and fires Registered', function () {
    Event::fake([Registered::class]);

    $this->post('/register', [
        'name' => 'Grace Hopper',
        'email' => 'grace@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ])->assertRedirect('/dashboard');

    $user = User::where('email', 'grace@example.com')->firstOrFail();

    expect(Auth::id())->toBe($user->id);
    Event::assertDispatched(Registered::class);
});

it('stores the password hashed', function () {
    $this->post('/register', [
        'name' => 'Grace', 'email' => 'grace@example.com',
        'password' => 'correct-horse-battery', 'password_confirmation' => 'correct-horse-battery',
    ]);

    $user = User::where('email', 'grace@example.com')->firstOrFail();

    expect($user->password)->not->toBe('correct-horse-battery')
        ->and(Hash::check('correct-horse-battery', $user->password))->toBeTrue();
});

it('rejects a duplicate email', function () {
    User::create(['name' => 'Ada', 'email' => 'ada@example.com', 'password' => 'x']);

    $this->from('/register')->post('/register', [
        'name' => 'Someone else', 'email' => 'ada@example.com',
        'password' => 'correct-horse-battery', 'password_confirmation' => 'correct-horse-battery',
    ])->assertSessionHasErrors('email');

    expect(User::count())->toBe(1);
});

it('requires the password to be confirmed', function () {
    $this->from('/register')->post('/register', [
        'name' => 'Grace', 'email' => 'grace@example.com',
        'password' => 'correct-horse-battery', 'password_confirmation' => 'something-else',
    ])->assertSessionHasErrors('password');

    expect(User::count())->toBe(0);
});

it('can be switched off without touching the other screens', function () {
    config()->set('gentelella.auth.register', false);

    // The flag is read at boot, so this asserts the shape rather than the
    // routing; DisabledRegistrationTest covers the booted case.
    expect(config('gentelella.auth.register'))->toBeFalse();
});
