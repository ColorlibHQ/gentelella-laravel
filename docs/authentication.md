# Authentication

```php
// config/gentelella.php
'auth' => ['enabled' => true],
```

That is the whole setup. Sign-in, registration and password reset on the template's own auth
markup.

## Route names

Registered under Laravel's conventional names — `login`, `register`, `password.request`,
`password.email`, `password.reset`, `password.store`, `logout` — because the framework's auth
middleware redirects to `login` **by name**, and the password broker emails a link to
`password.reset`. Anything else would work only when reached by hand.

## It never shadows auth you already have

Registration is deferred until every service provider has booted, which is when the application's
own route files have been loaded. Any screen whose route name already exists is skipped.

This ordering matters: a provider's `boot()` runs *before* route files load, so a guard checked
there would always see "no login route" and the package would claim `/login` — and since Laravel
matches routes in registration order, the application's own sign-in would never be reached.

An app on a starter kit can switch this on and keep its own sign-in.

## Switches

```php
'auth' => [
    'enabled'    => true,
    'prefix'     => '',          // URL prefix, e.g. 'account'
    'middleware' => ['web'],
    'register'   => false,       // closed system: sign-in only
    'reset'      => true,
    'home'       => '/admin',    // where a signed-in user lands
    'throttle'   => 5,           // failed sign-ins per minute; 0 disables
],
```

## What the screens do

- **Failed sign-ins are rate limited**, keyed by email *and* IP, so one attacker cannot lock a real
  user out of their own account.
- **The session id is rotated on sign-in**, so a session fixed beforehand is worthless.
- **A wrong password and an unknown address give the same message**, and a reset request answers the
  same either way — neither form can be used to find out which accounts exist.
- **Resetting rotates the remember token**, invalidating any "remember me" cookie issued before it.
- Passwords are validated with `Password::defaults()`, so your application's own policy applies.
- Registration reads the user model from `auth.providers.users.model` rather than assuming
  `App\Models\User`, and refuses if that model cannot authenticate.

## Owning the code

```bash
php artisan gentelella:make-auth
```

| Written to | |
|---|---|
| `app/Http/Controllers/Auth/` | Controllers, rewritten to your namespace |
| `resources/views/vendor/gentelella/auth/` | The four screens |
| `routes/gentelella-auth.php` | A route file |

Then require the route file and set `'auth' => ['enabled' => false]` so the two do not both
register.

## Using the views with your own auth

You do not have to enable anything. Point your existing controllers at the views:

```php
return view('gentelella::auth.login');
```

They expect the conventional route names to exist, and guard the optional links (`register`,
`password.request`) with `Route::has()`, so a partial set is fine.
