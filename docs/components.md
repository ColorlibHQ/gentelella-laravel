# Components

25 anonymous Blade components, extracted from the HTML edition's own markup rather than designed
fresh — so the compiled CSS applies unchanged and the two editions look identical.

```blade
<x-gentelella::page-header title="Customers" pretitle="Data">
    <x-slot:actions>
        <x-gentelella::btn variant="primary">New customer</x-gentelella::btn>
    </x-slot>
</x-gentelella::page-header>

<x-gentelella::card title="All customers" subtitle="Sortable, searchable, paginated." flush>
    <x-slot:options><x-gentelella::card-options /></x-slot>

    <x-gentelella::table datatable :page-length="10" selectable export="customers">
        <tbody>…</tbody>
    </x-gentelella::table>
</x-gentelella::card>
```

## The set

| Group | |
|---|---|
| Layout | `card`, `card-options`, `page-header`, `divider`, `table` |
| Data | `stat`, `progress`, `status`, `badge`, `chip`, `avatar`, `timeline`, `timeline-item` |
| Navigation | `tabs`, `accordion`, `accordion-item`, `list-group`, `list-group-item` |
| Feedback | `banner`, `empty-state`, `skeleton`, `spinner` |
| Controls | `btn`, `toggle` |
| Internal | `field-group`, `auth.card` — used by the field and auth views |

## Attributes pass through

Every component merges extra attributes rather than replacing them:

```blade
<x-gentelella::card class="chart-card" data-chart="revenue">…</x-gentelella::card>
{{-- <div class="card chart-card" data-chart="revenue"> --}}
```

## Notable behaviour

- **`card`** renders a header only when there is something to put in it. `flush` skips the
  `.card-body` for content that manages its own padding — a table wrapper, say.
- **`btn`** renders an `<a>` when given `href`, otherwise a `<button>`.
- **`progress`** clamps `value` to 0–100 and strips its `tone` to `[a-z0-9-]`. Both land inside an
  inline `style`, and both routinely come from database values.
- **`stat`** infers the change direction from the sign unless `direction` is given, and clamps
  sparkline bar heights the same way.
- **`accordion-item`** is a native `<details>`, so it works with JavaScript disabled.
- **`toggle`** is a `role="switch"` with `aria-checked`.

## Overlays

Use the JavaScript the template already ships — `showModal()`, `showToast()`, `openMenu()` — rather
than hand-rolling markup. They handle outside-click, escape and focus return.

## Customising

```bash
php artisan vendor:publish --tag=gentelella-views
```

Copies every view into `resources/views/vendor/gentelella`, where Laravel will prefer them.
