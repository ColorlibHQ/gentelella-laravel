# Changelog

All notable changes to `colorlibhq/gentelella-laravel` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
