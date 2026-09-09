# Changelog

All notable changes to `colorlibhq/gentelella-laravel` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

First working version. Not yet tagged.

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

### Not yet implemented

- Field types: date range, multi-select, rich text, file upload, avatar upload, OTP, repeatable,
  checklist, month, week.
- List filters and the reorder operation.
