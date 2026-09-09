<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Auth\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    use RendersAuthViews;

    public function request(): View
    {
        return $this->authView('gentelella::auth.forgot-password');
    }

    public function email(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        // Always the same answer. Reporting "we have no such user" turns the
        // form into a way to enumerate accounts.
        return back()->with('status', __($status === Password::RESET_LINK_SENT
            ? $status
            : 'passwords.sent'));
    }

    public function reset(Request $request, string $token): View
    {
        return $this->authView('gentelella::auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (CanResetPassword $user, string $password): void {
                // The broker's contract is CanResetPassword; persisting and the
                // PasswordReset event both need more than that, so narrow once
                // rather than assuming.
                if (! $user instanceof Model || ! $user instanceof Authenticatable) {
                    return;
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                    // Rotating this invalidates any "remember me" cookie issued
                    // before the reset.
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', __($status));
    }
}
