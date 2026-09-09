<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Auth\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use RendersAuthViews;

    public function create(): View
    {
        return $this->authView('gentelella::auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            // One message for both a wrong password and an unknown address, so
            // the form cannot be used to find out which accounts exist.
            //
            // The package carries its own wording rather than leaning on
            // Laravel's auth.failed: those lang files are not published by
            // default, and the key would render raw.
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        // Rotates the session id, so a session fixed before login is worthless.
        $request->session()->regenerate();

        return redirect()->intended($this->home());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($this->home());
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        $max = (int) config('gentelella.auth.throttle', 5);

        if ($max < 1 || ! RateLimiter::tooManyAttempts($this->throttleKey($request), $max)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => __('Too many attempts. Try again in :seconds seconds.', [
                'seconds' => RateLimiter::availableIn($this->throttleKey($request)),
            ]),
        ]);
    }

    /** Keyed by address and origin, so one attacker cannot lock out a real user. */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->string('email')).'|'.$request->ip());
    }
}
