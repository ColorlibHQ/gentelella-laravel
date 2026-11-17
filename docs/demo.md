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

## Vite setup for the demo

Most of the demo pages are static markup, but 13 of them carry a script that imports the design
system's modules (toasts, modals, menus, the kanban board, the file manager, Leaflet for the map).
In the HTML edition those imports resolve against the template's own source tree. In a Laravel app
there is no such tree, so the layout emits an
[import map](https://developer.mozilla.org/docs/Web/HTML/Element/script/type/importmap) that sends
each specifier to the file Vite built. For that, Vite has to build them: add the modules to your
`vite.config.js` inputs and tell Rollup to keep their exports.

```js
// vite.config.js
const demoModules = [
    'details', 'file-manager', 'highlight', 'kanban', 'markup', 'menus', 'modal',
    'data-adapter', 'product-images', 'product-mockups', 'toast',
].map((name) => `node_modules/gentelella/src/v4/${name}.js`)

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/gentelella.js',
                ...demoModules,
                'node_modules/leaflet/dist/leaflet-src.js',
                'node_modules/leaflet/dist/leaflet.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Without this Rollup drops the exports of an entry nothing else imports,
        // and the pages fail with "does not provide an export named …".
        rollupOptions: { preserveEntrySignatures: 'exports-only' },
    },
})
```

`leaflet` is installed with `gentelella`, so there is nothing extra to `npm install`.

The list lives in `gentelella.demo_modules` (and `gentelella.demo_stylesheets` for the map's CSS),
so you can trim it if you only keep some pages. The import map is only written when `demo` is on,
and a module missing from the build is skipped rather than raising an error: that page simply
loses its behaviour until you add the entry. After changing `vite.config.js`, run `npm run build`.

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

A fresh Laravel app answers `/` with its own welcome page. For a demo, point it at the sign-in
screen instead — but send people who are already signed in to the dashboard:

```php
// routes/web.php
Route::get('/', fn () => auth()->check()
    ? redirect(config('gentelella.auth.home'))
    : redirect()->route('login'));
```

A plain `Route::redirect('/', '/login')` looks equivalent and is not: Laravel's `guest` middleware
bounces an authenticated visitor off the sign-in screen, so `/` sends them to `/login`, `/login`
sends them back to `/`, and the browser gives up. The package points that middleware at
`auth.home` so the loop cannot form, but routing on the auth state is clearer anyway.

The package does not claim `/` for you: taking the root of someone's application would be a
surprise.

## Turning it off

Set `'demo' => false`. The routes, the migrations and the seeder all go with it.
