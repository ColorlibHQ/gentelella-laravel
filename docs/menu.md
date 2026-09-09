# Menu

One array in `config/gentelella.php`. Leave it `null` to use the bundled demo sidebar.

```php
'menu' => [
    [
        'label' => 'General',
        'items' => [
            ['key' => 'dashboard', 'text' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
            [
                'text' => 'Catalogue',
                'icon' => 'shop',
                'children' => [
                    ['key' => 'products', 'text' => 'Products', 'route' => 'admin.products.index'],
                    ['key' => 'categories', 'text' => 'Categories', 'route' => 'admin.categories.index',
                     'can' => 'manage-catalogue'],
                ],
            ],
        ],
    ],
],
```

## Item keys

| Key | |
|---|---|
| `text` | Label. Required. |
| `key` | Matched against the page's `page_key` to mark the item active |
| `icon` | Name from `resources/icons.php` |
| `badge` | `['text' => 'Hot', 'class' => 'badge-red']` |
| `can` | Ability, or list of abilities, the user must pass |
| `children` | Submenu items; a parent has no target of its own |

## Targets

Exactly one of:

| Key | |
|---|---|
| `url` | Used verbatim |
| `route` | Route name, plus optional `route_params` |
| `page` | A bundled demo page slug |

An unresolvable target renders as `#` rather than throwing. A menu entry pointing at a route the
app has not defined yet should be inert, not a 500 on every page.

## Filters

Applied in order, configurable via `gentelella.filters`:

| Filter | |
|---|---|
| `GateFilter` | Drops items whose `can` the user fails. No `can` means always visible — authorisation is explicit, never inferred. |
| `HrefFilter` | Resolves the target to a URL |
| `ActiveFilter` | Marks the active leaf; opens the parent of an active child |
| `SearchFilter` | Collects reachable leaves for ⌘K and breadcrumb resolution |

A parent whose children were all dropped is dropped too, so no empty submenus render.

## Icons

Inline SVG, keyed by name, in `resources/icons.php` — generated from `ICONS` in the HTML edition's
`src/v4/shell-render.js`. To add one, add it upstream and re-run `npm run export:php`; editing the
generated file directly will be overwritten.

An unknown icon name renders nothing rather than raising.
