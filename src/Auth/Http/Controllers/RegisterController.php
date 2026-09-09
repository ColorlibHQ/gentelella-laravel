<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Auth\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RendersAuthViews;

    public function create(): View
    {
        return $this->authView('gentelella::auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $model = $this->userModel();
        $table = (new $model)->getTable();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($table, 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $model::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // A user model that cannot authenticate would leave the account created
        // but unusable, which is worse than refusing outright.
        if (! $user instanceof Authenticatable) {
            throw new \RuntimeException($model.' must implement '.Authenticatable::class.' to be registered.');
        }

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->home());
    }

    /**
     * The application's own user model, read from the auth config rather than
     * assumed to be App\Models\User.
     *
     * @return class-string<Model>
     */
    private function userModel(): string
    {
        /** @var class-string<Model>|null $model */
        $model = config('auth.providers.users.model');

        if ($model === null || ! class_exists($model)) {
            throw new \RuntimeException('No user model configured at auth.providers.users.model.');
        }

        return $model;
    }
}
