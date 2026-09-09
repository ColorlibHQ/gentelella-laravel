<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'correct-horse-battery',
    ]);
});

it('renders the forgot-password screen', function () {
    $this->get('/forgot-password')->assertOk()->assertSee('Forgot your password?');
});

it('emails a reset link', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'ada@example.com'])->assertSessionHas('status');

    Notification::assertSentTo($this->user, ResetPassword::class);
});

it('answers the same way for an address it does not know', function () {
    // Otherwise the form reports which addresses have accounts.
    Notification::fake();

    $known = $this->post('/forgot-password', ['email' => 'ada@example.com']);
    $unknown = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

    expect($unknown->getStatusCode())->toBe($known->getStatusCode())
        ->and(session('status'))->not->toBeNull();

    Notification::assertSentTimes(ResetPassword::class, 1);
});

it('renders the reset screen with the token', function () {
    $this->get('/reset-password/some-token?email=ada@example.com')
        ->assertOk()
        ->assertSee('value="some-token"', false)
        ->assertSee('value="ada@example.com"', false);
});

it('resets the password with a valid token', function () {
    $token = Password::createToken($this->user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'ada@example.com',
        'password' => 'a-brand-new-secret',
        'password_confirmation' => 'a-brand-new-secret',
    ])->assertRedirect(route('login'))->assertSessionHas('status');

    expect(Hash::check('a-brand-new-secret', $this->user->fresh()->password))->toBeTrue();
});

it('refuses an invalid token', function () {
    $this->from('/reset-password/bad')->post('/reset-password', [
        'token' => 'not-a-real-token',
        'email' => 'ada@example.com',
        'password' => 'a-brand-new-secret',
        'password_confirmation' => 'a-brand-new-secret',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('correct-horse-battery', $this->user->fresh()->password))->toBeTrue();
});

it('rotates the remember token so old sessions cannot survive a reset', function () {
    $this->user->forceFill(['remember_token' => 'old-token'])->save();
    $token = Password::createToken($this->user);

    $this->post('/reset-password', [
        'token' => $token, 'email' => 'ada@example.com',
        'password' => 'a-brand-new-secret', 'password_confirmation' => 'a-brand-new-secret',
    ]);

    expect($this->user->fresh()->remember_token)->not->toBe('old-token');
});
