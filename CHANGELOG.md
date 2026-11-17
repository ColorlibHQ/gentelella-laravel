# Changelog

All notable changes to `colorlibhq/gentelella-laravel` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.5] - 2026-11-17

### Changed

- The documented and installer-advertised `gentelella` floor is now `^4.2.2`, which carries the
  mobile overflow fixes and the relative-base build fix.
- The demo layout no longer mirrors Gentelella 4.2.1's 390px overflow rules; the design system
  ships them now.

[1.0.5]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.5

## [1.0.4] - 2026-11-17

### Fixed

- Thirteen demo pages (contacts, e_commerce, file_manager, general_elements, invoice, kanban, map,
  orders, playground, product_detail, projects, theme, user_management) were inert in a Laravel
  app: their scripts import `/src/v4/*.js` and `leaflet`, which resolve against the template's
  source tree and 404 here, so toasts, modals, menus, the kanban board, the file manager and the
  map did nothing. In demo mode the layout now writes an import map to the files Vite built, and the
  map page loads Leaflet's stylesheet. This needs the modules in your Vite inputs; see
  [docs/demo.md](docs/demo.md#vite-setup-for-the-demo). New config: `demo_modules`,
  `demo_stylesheets`.
- `general_elements` and `orders` overflowed 390px screens. The rules Gentelella 4.2.1 added for
  this are mirrored in the demo layout, so the demo is correct on any `gentelella@^4.2.0`.

[1.0.4]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.4

## [1.0.3] - 2026-11-17

### Fixed

- The Playground demo page overflowed narrow screens; synced with Gentelella 4.2.1.
- The test fixtures directory is now `tests/Fixtures`, matching its namespace, so the suite loads on
  case-sensitive filesystems. CI on Linux had been failing since 1.0.0.

### Changed

- Development dependencies refreshed: Pest 5 (on PHP 8.4+; PHP 8.3 keeps resolving Pest 4),
  Pint 1.32, Larastan 3.15, Testbench 11.3 and Playwright 1.65. The package's runtime requirements
  are unchanged.

[1.0.3]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.3

## [1.0.2] - 2026-09-10

### Fixed

- Signing out landed on `auth.home`. The session really was cleared, but that is a page you can
  still see while signed out, so it looked as though the button had done nothing. It now returns to
  the sign-in screen and says so.

[1.0.2]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.2

## [1.0.1] - 2026-09-09

### Fixed

- An application whose `/` points at the sign-in screen looped forever for anyone signed in:
  Laravel's `guest` middleware bounced them off `/login` to its own default, which redirected back.
  `RedirectIfAuthenticated` now points at `gentelella.auth.home`.

[1.0.1]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.1

## [1.0.0] - 2026-09-09

First release.

### Added

- **Shell** — `gentelella::page` and `gentelella::layouts.blank`, with sidebar, topbar, footer and
  breadcrumbs. DOM order matches the HTML edition exactly, so the compiled CSS applies unchanged.
- **Menu** — a config-driven sidebar with Gate, Href, Active and Search filters. The default menu
  and the icon set are generated from `NAV` and `ICONS` in the HTML edition, so the two cannot
  drift.
- **Components** — 25 anonymous Blade components extracted from the template's own markup.
- **CRUD engine** — `ResourceController`, a fluent `Panel`, five operations as composable traits,
  14 column types, 18 field types, and server-side DataTables backed by `DataTableResponder`.
- **`Route::gentelella()`** — registers a panel's routes, skipping any operation the controller
  does not implement.
- **Auth** — sign-in, registration and password reset on the template's auth markup, under
  Laravel's conventional route names, with throttling and no account enumeration.
- **Demo** — all 58 pages of the HTML edition, 57 generated and one (Tables) backed by a real CRUD
  panel over seeded data.
- **Commands** — `gentelella:install`, `gentelella:crud`, `gentelella:make-auth`,
  `gentelella:demo`.
- **Docs** — [docs/](docs/README.md), with tests that check every documented command, config key
  and type against the code.

### Notes

- Requires PHP 8.3+ and Laravel 13.
- No `doctrine/dbal`: schema introspection uses Laravel's own schema builder.
- Two npm dependencies — `gentelella` and `sass`. ECharts, DataTables and Leaflet load lazily on
  the pages that use them.

### Also in this release

- **List filters** — text, select, boolean and date-range controls, plus an `apply` closure for
  anything else. Sent with every request, so a filter survives paging and sorting.
- **Reorder operation** — up/down rather than drag: no library, works on a phone, and can be driven
  from the keyboard.
- **CSV export** — the whole filtered result set, streamed in chunks, matching what the table shows.
- **Demo account** — created by `gentelella:demo` and filled into the sign-in screen, gated on demo
  mode so a real site cannot advertise a login by forgetting a setting.
- **Browser smoke test** — `npm run smoke`. The PHP suite asserts markup, and markup that renders
  perfectly can still do nothing; this drives a real browser.

[1.0.0]: https://github.com/ColorlibHQ/gentelella-laravel/releases/tag/v1.0.0
