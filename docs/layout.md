# Layout

## The admin shell

```blade
@extends('gentelella::page')

@section('title', 'Products')
@section('page_key', 'products')
@section('breadcrumb', 'Home > Catalogue > Products')

@section('content')
    …
@endsection
```

| Section | |
|---|---|
| `title` | Prepended to the configured base title |
| `page_key` | Matched against the menu to mark an item active |
| `breadcrumb` | `>`-separated trail; see below |
| `content` | The page body, inside `.page-wrapper` |

A controller may pass `$pageKey` and `$breadcrumb` as view data instead; **the variables win over
the sections.**

## Bare pages

```blade
@extends('gentelella::layouts.blank')
```

No sidebar, no topbar — for auth screens, error pages and landing pages. Same `<head>`, so the
theme, fonts and assets are identical.

## DOM order

The layout emits exactly what the HTML edition's build step produces, so the compiled CSS applies
unchanged:

```html
<a class="skip-link">…</a>
<aside class="sidebar">…</aside>
<header class="topbar">…</header>
<main id="main-content" class="main">
  <div class="page-wrapper">…your content…</div>
  <footer class="footer">…</footer>
</main>
```

Note the footer is **inside** `<main>`.

## Breadcrumbs

```
Home > Projects|/projects > Acme Redesign
```

Every crumb but the last resolves to a link, in this order:

1. an explicit target after `|`
2. an exact label match against the menu
3. neither — plain text, no link

The last crumb is the current page: never a link, always `aria-current="page"`.

Prefer a level that points somewhere. If a segment is a pure grouping with no landing page, drop it
— `Home > Kanban`, not `Home > Apps > Kanban`.

### A Blade gotcha

Blade runs inline `@section` values through `e()`, so `@section('breadcrumb', 'Home > Forms')`
arrives as `Home &gt; Forms`. The layout decodes before splitting and re-escapes each crumb on
output, so this works — but it is why passing the value as view data behaves slightly differently
from a section, and why any `@section` carrying markup-significant characters needs care.

## The shell config island

Shell pages emit a JSON island in `<head>`:

```html
<script type="application/json" id="gentelella-shell-config">
  {"links": {...}, "pages": [{"label": "Products", "section": "Catalogue", "href": "/admin/products"}]}
</script>
```

The design system reads it once. It exists because the JavaScript ships with the *static* template's
menu compiled in — without the island the ⌘K palette lists Gentelella's demo pages and every result
404s in your app. `pages` comes from your configured menu (only entries that resolve to a real URL),
and `links` from [`gentelella.links`](configuration.md#shell-links).

`links.logout` also changes how signing out works: given a URL, the design system POSTs a form
carrying the CSRF token from `<meta name="csrf-token">` rather than navigating, because signing out
on a GET is CSRF-able.

Bare pages emit no island — there is no shell on them to configure.

## `data-shell`

The shell layout puts `data-shell="admin"` on `<body>`. That attribute is what `mountShell()` looks
for; without it the design system wires nothing at all — no sidebar accordion, no mobile drawer, no
theme toggle, no topbar panels. It is markup that looks right and does nothing.

You get it automatically from `gentelella::page`. A hand-rolled layout has to set it.

## Theming

Colours are CSS custom properties, so overriding one is a plain declaration in
`resources/css/gentelella.scss`:

```scss
:root { --accent: #1abb9c; }
[data-theme='dark'] { --accent: #21d4ae; }
```

Dark mode is applied to `<html>` by an inline script before the body renders, so it never flashes
the wrong way round. The same script restores text direction for RTL.
