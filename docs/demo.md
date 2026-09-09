# The bundled demo

Every page from the HTML edition, served from your own application.

```php
// config/gentelella.php
'demo' => true,
```

```bash
php artisan gentelella:demo   # migrate + seed the demo data
```

Browse `/demo`. Off by default, so a consumer app ships none of it.

## What is in it

| | |
|---|---|
| 58 pages | Dashboards, apps, e-commerce, forms, charts, UI library, auth, errors |
| 57 generated | From `production/*.html` in the HTML edition |
| 1 CRUD-backed | **Tables** is a real panel over a seeded catalogue |

Route names are `gentelella.demo.<slug>`, which is what the menu's `page` entries resolve against —
so the sidebar and the routes cannot drift.

## Tables is real

Not static markup: 25 seeded products with server-side search, sorting and paging, and working
create, edit and delete. It is the same `ResourceController` you would write yourself — see
`src/Demo/Http/ProductController.php`.

Demo tables are prefixed `gentelella_demo_`, so turning the demo on cannot shadow your own
`products` or `categories`.

## Regenerating the pages

The static pages are generated, not maintained here:

```bash
# in ColorlibHQ/gentelella
npm run export:demo -- --out ../gentelella-laravel
```

Two things that conversion has to get right, both of which were bugs first:

- **Each page keeps its own `<script>`.** 25 of the 58 pages put their behaviour in a script after
  `</main>`; it is emitted into a `@push('scripts')` stack. Dropping it renders a page that looks
  right and does nothing.
- **The body is wrapped in a verbatim block.** These pages are static markup, and without it Blade
  would eat the literal `@use` on the landing page and the `@media` rules in the two pages that
  carry a `<style>` block.

Pages the package owns by hand — currently just `tables` — are listed in `CRUD_OWNED` in the export
script and are never overwritten.

## The demo account

A public demo needs a way in. `gentelella:demo` creates one, and the sign-in screen fills it in:

```php
'demo_user' => [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
    'password' => 'Gentelella-Demo-7Fq2-Vx9k-Rm4t',
],
```

Set it to `null` to run the demo without an account.

**The prefill is gated on `demo`, not on the block being present.** An application that leaves the
default in its published config cannot end up advertising a login on a real site.

The default password is 30 characters, mixed case with digits, and does not appear in the breach
corpora browsers check — a short or reused one would make Chrome flag the account on every sign-in.
Re-running `gentelella:demo` resets it, which is what you want after somebody has changed it on a
public demo.

**A password field served over plain HTTP is marked "Not secure" whatever the password is.** A demo
with an account needs TLS.

## Landing on the sign-in screen

A fresh Laravel app answers `/` with its own welcome page. For a demo, point it at the login screen
instead:

```php
// routes/web.php
Route::redirect('/', '/login');
```

The package does not do this for you: claiming `/` in someone's application would be a surprise.

## Turning it off

Set `'demo' => false`. The routes, the migrations and the seeder all go with it.
