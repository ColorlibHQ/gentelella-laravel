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

## Turning it off

Set `'demo' => false`. The routes, the migrations and the seeder all go with it.
