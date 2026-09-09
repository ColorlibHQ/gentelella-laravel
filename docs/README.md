# Gentelella for Laravel — documentation

These files are the source of truth for authors: they sit next to the code and
`tests/Feature/DocumentationTest.php` checks their claims against it. Readers get
the same content at **<https://gentelella.colorlib.com/docs/laravel/>**, generated
from here by `node scripts/export-docs.mjs` — so the two cannot drift.

| | |
|---|---|
| [Installation](installation.md) | Requirements, install, Vite wiring, first page |
| [Configuration](configuration.md) | Every key in `config/gentelella.php` |
| [Layout](layout.md) | Page layouts, sections, breadcrumbs, theming |
| [Menu](menu.md) | The sidebar, item shapes, filters |
| [Components](components.md) | The 25 Blade components |
| [CRUD](crud.md) | Panels, operations, routes, server-side tables |
| [Columns](columns.md) | The 14 list column types |
| [Fields](fields.md) | The 18 form field types |
| [Authentication](authentication.md) | Sign-in, registration, password reset |
| [Demo](demo.md) | The bundled 58-page showcase |
| [Errors & localisation](errors-and-localisation.md) | Error pages, translating the strings |
| [Commands](commands.md) | Every Artisan command |
| [Deployment](deployment.md) | Running the demo on a real host |

## How the pieces fit

```
config/gentelella.php        what the shell looks like, what is switched on
    │
    ├── menu ──────────────► sidebar, ⌘K palette, breadcrumb link resolution
    ├── auth ──────────────► login / register / reset routes
    └── demo ──────────────► the 58-page showcase and its data

gentelella::page             the admin shell layout you extend
gentelella::layouts.blank    bare pages — auth, errors, landing

<x-gentelella::*>            25 components, extracted from the template's markup
ResourceController           a CRUD screen described in one setup() method
```

Two of these are **generated** from the HTML edition rather than written here, so the two editions
cannot drift:

| File | Source | Regenerate |
|---|---|---|
| `resources/menu.php`, `resources/icons.php` | `NAV` / `ICONS` in `src/v4/shell-render.js` | `npm run export:php` |
| `resources/views/demo/*`, `resources/demo-pages.php` | `production/*.html` | `npm run export:demo` |

Both commands live in [ColorlibHQ/gentelella](https://github.com/ColorlibHQ/gentelella) and take
`--out` pointing at this package.
