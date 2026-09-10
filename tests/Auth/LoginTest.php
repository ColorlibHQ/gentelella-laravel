<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'correct-horse-battery',
    ]);
});

it('registers the conventional route names', function () {
    // The framework's auth middleware redirects to `login` by name, and the
    // password broker emails a link to `password.reset`.
    expect(Route::has('login'))->toBeTrue()
        ->and(Route::has('logout'))->toBeTrue()
        ->and(Route::has('register'))->toBeTrue()
        ->and(Route::has('password.request'))->toBeTrue()
        ->and(Route::has('password.reset'))->toBeTrue()
        ->and(Route::has('password.store'))->toBeTrue();
});

it('renders the sign-in screen on the template auth markup', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('class="auth-page"', false)
        ->assertSee('class="auth-card"', false)
        ->assertSee('Welcome back')
        ->assertSee('name="_token"', false);
});

it('signs a user in and sends them to the configured home', function () {
    $this->post('/login', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery'])
        ->assertRedirect('/dashboard');

    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($this->user->id);
});

it('rotates the session id on sign-in', function () {
    // A session fixed before login must not survive it.
    $this->get('/login');
    $before = session()->getId();

    $this->post('/login', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery']);

    expect(session()->getId())->not->toBe($before);
});

it('gives the same message for a wrong password and an unknown address', function () {
    $wrongPassword = $this->from('/login')
        ->post('/login', ['email' => 'ada@example.com', 'password' => 'nope']);

    $unknownUser = $this->from('/login')
        ->post('/login', ['email' => 'nobody@example.com', 'password' => 'nope']);

    $a = session('errors')?->get('email');
    $wrongPassword->assertRedirect('/login');
    $unknownUser->assertRedirect('/login');

    expect($a)->toBe(session('errors')?->get('email'))
        ->and(Auth::check())->toBeFalse();
});

it('throttles repeated failures', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['email' => 'ada@example.com', 'password' => 'nope']);
    }

    $this->from('/login')
        ->post('/login', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery'])
        ->assertRedirect('/login');

    // The correct password is refused too, so guessing cannot continue.
    expect(Auth::check())->toBeFalse()
        ->and(session('errors')->get('email')[0])->toContain('seconds');
});

it('clears the throttle counter after a successful sign-in', function () {
    $this->post('/login', ['email' => 'ada@example.com', 'password' => 'nope']);
    $this->post('/login', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery']);

    expect(RateLimiter::attempts('ada@example.com|127.0.0.1'))->toBe(0);
});

it('keys the throttle by address and origin', function () {
    // One attacker hammering an address from their own IP must not lock the
    // real owner out from theirs.
    for ($i = 0; $i < 6; $i++) {
        $this->post('/login', ['email' => 'ada@example.com', 'password' => 'nope'], ['REMOTE_ADDR' => '10.0.0.9']);
    }

    $this->post('/login', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery']);

    expect(Auth::check())->toBeTrue();
});

it('signs a user out and invalidates the session', function () {
    $this->actingAs($this->user);

    $this->post('/logout')->assertRedirect(route('login'));

    expect(Auth::check())->toBeFalse();
});

it('keeps signed-in users away from the sign-in screen', function () {
    $this->actingAs($this->user)->get('/login')->assertRedirect();
});

it('requires authentication to sign out', function () {
    $this->post('/logout')->assertRedirect(route('login'));
});

it('redirects a signed-in visitor away from the sign-in screen to the configured home', function () {
    $this->actingAs($this->user)->get('/login')->assertRedirect('/dashboard');
});

it('does not bounce between / and /login for a signed-in visitor', function () {
    // The exact loop: "/" redirects to the sign-in screen, and the sign-in
    // screen redirects an authenticated visitor back to "/".
    Route::redirect('/', '/login');

    $this->actingAs($this->user)
        ->get('/login')
        ->assertRedirect(config('gentelella.auth.home'));

    expect(config('gentelella.auth.home'))->not->toBe('/');
});

it('lands on the sign-in screen after signing out', function () {
    // Redirecting to `home` leaves someone looking at a page they can still
    // see while signed out — the same dashboard, with nothing to say it
    // worked. It reads as a broken button.
    $this->actingAs($this->user)
        ->post('/logout')
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    expect(Auth::check())->toBeFalse();
});

it('says so on the sign-in screen', function () {
    $this->actingAs($this->user)->post('/logout');

    $this->followingRedirects()->get('/login')->assertSee('You have been signed out.');
});
