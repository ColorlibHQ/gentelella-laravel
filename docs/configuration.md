# Configuration

Everything lives in `config/gentelella.php`, published by `php artisan gentelella:install`.

## Branding

| Key | Default | |
|---|---|---|
| `title` | `Gentelella` | Base `<title>`; each page prepends its own section |
| `title_prefix` / `title_postfix` | `''` | Wrap the base title |
| `brand_name` | `Gentelella` | Sidebar brand text |
| `brand_suffix` | `v4` | Muted text after the brand |
| `brand_initial` | `G` | Letter in the brand square |

## Document

| Key | Default | |
|---|---|---|
| `favicon` | `null` | Path under `public/`, resolved with `asset()` |
| `manifest` | `null` | PWA manifest path |
| `apple_touch_icon` | `null` | Touch icon path |
| `google_fonts` | `true` | Load Inter from Google Fonts |
| `service_worker` | `false` | Let the design system register `/sw.js` |

`manifest`, `apple_touch_icon` and `service_worker` are all off by default for the same reason:
pointing at a file nobody published only buys a 404 on every page load. Turn `service_worker` on
once you have actually published a `sw.js` to `public/`.

## Routing

| Key | Default | |
|---|---|---|
| `middleware` | `['web']` | Wraps every route from `Route::gentelella()` |

It has to be session-backed. The forms post with CSRF, flash messages come back through the
session, and validation errors reach the views only via the web group's `ShareErrorsFromSession`.

## Menu

| Key | Default | |
|---|---|---|
| `menu` | `null` | `null` uses the bundled demo sidebar |
| `filters` | Gate, Href, Active, Search | Applied in order to every item |

See [Menu](menu.md).

## Topbar

| Key | Default | |
|---|---|---|
| `docs_url` | This package's docs | `null` hides the Docs button |
| `search_enabled` | `true` | ⌘K search box |
| `theme_toggle` | `true` | Light/dark switch |
| `notifications_enabled` | `true` | Bell |
| `messages_enabled` | `true` | Envelope |

## Shell links

Where the account menu and the ⌘K palette send people. Each value is a route name or an absolute
path; anything that resolves to neither is dropped, and that menu item does not appear.

| Key | Default |
|---|---|
| `links.profile` | `null` |
| `links.settings` | `null` |
| `links.theme` | `null` |
| `links.help` | `null` |
| `links.lock` | `null` |
| `links.logout` | `logout` |

Without these the design system falls back to the static template's own pages — `profile.html` and
friends — which do not exist in an application. See [Layout](layout.md#the-shell-config-island).

## Sidebar user block

| Key | Default | |
|---|---|---|
| `user.enabled` | `true` | Show the block at all |
| `user.name_field` | `name` | Attribute read off the authenticated user |
| `user.role_field` | `role` | Attribute read for the subtitle |
| `user.fallback_name` | `Guest` | Shown when nobody is signed in |
| `user.fallback_role` | `Visitor` | |

## Footer

| Key | Default | |
|---|---|---|
| `footer_left` | Colorlib credit | Rendered unescaped — it carries a link |
| `footer_right` | `null` | Falls back to the package version and licence |

## Authentication

| Key | Default | |
|---|---|---|
| `auth.enabled` | `false` | Register the auth routes |
| `auth.prefix` | `''` | URL prefix for them |
| `auth.middleware` | `['web']` | |
| `auth.register` | `true` | Offer registration |
| `auth.reset` | `true` | Offer password reset |
| `auth.home` | `/` | Where a signed-in user lands |
| `auth.throttle` | `5` | Failed sign-ins per minute; `0` disables |

See [Authentication](authentication.md).

## Demo

| Key | Default | |
|---|---|---|
| `demo` | `false` | Serve the bundled showcase |
| `demo_prefix` | `demo` | URL prefix |
| `demo_middleware` | `['web']` | |

See [Demo](demo.md).

## Assets

| Key | Default | |
|---|---|---|
| `vite` | `['resources/js/gentelella.js']` | Entries passed to `@vite` |
