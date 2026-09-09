<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Gentelella;

function enableDemoAccount(): array
{
    config()->set('gentelella.demo', true);
    app()->forgetInstance(Gentelella::class);

    return config('gentelella.demo_user');
}

it('offers no credentials unless demo mode is on', function () {
    // Gated on demo mode, not merely on the block being configured — otherwise
    // a real site could advertise a login by forgetting a setting.
    expect(config('gentelella.demo'))->toBeFalse()
        ->and(config('gentelella.demo_user'))->toBeArray()
        ->and(app(Gentelella::class)->demoCredentials())->toBeNull();
});

it('offers them once demo mode is on', function () {
    enableDemoAccount();

    expect(app(Gentelella::class)->demoCredentials())
        ->toHaveKeys(['name', 'email', 'password']);
});

it('offers none when the account is turned off', function () {
    config()->set('gentelella.demo', true);
    config()->set('gentelella.demo_user', null);
    app()->forgetInstance(Gentelella::class);

    expect(app(Gentelella::class)->demoCredentials())->toBeNull();
});

it('ships a password no browser will complain about', function () {
    // Long, mixed, and absent from the breach corpora Chrome checks against.
    // A short or reused default would flag on every sign-in.
    $password = config('gentelella.demo_user.password');

    expect(strlen($password))->toBeGreaterThanOrEqual(20)
        ->and($password)->toMatch('/[A-Z]/')
        ->and($password)->toMatch('/[a-z]/')
        ->and($password)->toMatch('/[0-9]/')
        ->and(strtolower($password))->not->toContain('password');
});

it('fills the sign-in form in for a visitor', function () {
    $demo = enableDemoAccount();

    $this->get('/login')
        ->assertOk()
        ->assertSee('value="'.$demo['email'].'"', false)
        ->assertSee('value="'.$demo['password'].'"', false)
        ->assertSee('This is a live demo.');
});

it('fills in nothing when demo mode is off', function () {
    $response = $this->get('/login')->assertOk();

    $response->assertDontSee(config('gentelella.demo_user.password'), false)
        ->assertDontSee('This is a live demo.');
});

it('lets a typed value win over the demo one', function () {
    enableDemoAccount();

    $this->from('/login')
        ->post('/login', ['email' => 'someone@else.test', 'password' => 'wrong'])
        ->assertRedirect('/login');

    $this->followingRedirects()->get('/login')
        ->assertSee('value="someone@else.test"', false);
});
