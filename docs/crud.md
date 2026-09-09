# CRUD

A screen is one `setup()` method. There is nothing generated to keep in sync — the panel *is* the
definition.

```php
class ProductController extends \ColorlibHQ\Gentelella\Crud\ResourceController
{
    protected function setup(): void
    {
        $this->panel
            ->model(Product::class)
            ->route('admin/products')
            ->entity('product', 'products')
            ->columns([
                ['name' => 'name', 'searchable' => true, 'strong' => true],
                ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title'],
                ['name' => 'price', 'type' => 'money', 'symbol' => '€'],
            ])
            ->fields([
                ['name' => 'name', 'rules' => 'required|max:255'],
                ['name' => 'category_id', 'type' => 'select_from_model', 'model' => Category::class,
                 'attribute' => 'title'],
                ['name' => 'price', 'type' => 'number', 'rules' => 'required|numeric|min:0'],
            ]);
    }
}
```

```php
Route::gentelella('admin/products', ProductController::class);
```

See [Columns](columns.md) and [Fields](fields.md) for the type catalogues.

## Panel methods

| Method | |
|---|---|
| `model(class)` | The Eloquent model |
| `route(uri, ?name)` | URI, and the route name if it differs from the URI with dots |
| `entity(singular, ?plural)` | Wording for headings and flash messages |
| `columns([...])` / `column(...)` | List columns |
| `fields([...])` / `field(...)` | Form fields |
| `query(fn ($q) => …)` | Narrow the base query — a tenant scope, a soft-delete filter |
| `withoutActions()` | Drop the per-row edit/delete column |

`query()` is applied before search, filtering and ordering, and is included in the unfiltered
total, so it reads as "the rows this panel is about".

### Route names

`route('admin/products')` implies the route name `admin.products`, which is what
`Route::gentelella('admin/products', …)` produces. If you register under a different `as`, tell the
panel:

```php
->route('tables', 'gentelella.demo.tables')
```

Otherwise the views build links to a name that was never registered.

## Operations

Each is a trait on `ResourceController`. Compose only what you want; the route macro registers only
what exists, so dropping delete leaves no delete route rather than one that 500s.

| Trait | Routes |
|---|---|
| `ListRecords` | `index`, `data` |
| `CreateRecord` | `create`, `store` |
| `UpdateRecord` | `edit`, `update` |
| `DeleteRecord` | `destroy` |
| `ShowRecord` | `show` |

```php
class ReportController extends Controller
{
    use ListRecords;   // read-only: no create, edit or delete routes exist
}
```

`Route::gentelella()` accepts `only`, `except`, `as` and `middleware`.

## Server-side tables

The list screen paints immediately with an empty table and pulls rows from a JSON endpoint, so the
first byte never waits on the query. Search, ordering and paging all happen in SQL.

Deliberate behaviours:

- **Page length is capped** at `DataTableResponder::MAX_PAGE_LENGTH` (200). `length` comes from the
  query string, and DataTables sends `-1` for "all" — honouring either literally lets a stranger
  ask for the whole table.
- **`%` and `_` in a search term are literals.** They are escaped and the query states an explicit
  `ESCAPE` clause, because SQLite defines no default one; without it a search for `%` quietly
  returns every row.
- **Order direction is never interpolated.** Anything that is not `desc` is `asc`.
- **`draw` is echoed back as an integer**, so request text cannot reach the response.
- **Relationship columns are eager-loaded** — rendering a page is a fixed number of queries.
- **Ordering by a to-many relationship is refused** rather than joined. There is no single value to
  sort on, and a join would silently duplicate rows. A `belongsTo` is ordered with a correlated
  subquery.

## Reordering

```php
$this->panel->model(Slide::class)->route('admin/slides')->reorderable('position');
```

Adds a Reorder button to the list header and a screen where rows move with up and down buttons —
no drag library, works on a phone, and can be driven from the keyboard.

Positions are written from the submitted sequence rather than trusted from the request, and the
whole update runs in a transaction so a failure halfway cannot leave the list half-renumbered.

The route exists on every `ResourceController`; a panel that never called `reorderable()` answers
404 rather than raising.

## Export

Every panel gets a CSV export at `<route>.export`, and the list table is pointed at it
automatically.

It re-runs the query the table is showing — search, filters and ordering included — and streams it
in chunks, so the file matches the screen it came from and a large table is not loaded into memory.
The actions column is left out, and cell markup is reduced to text.

This is why export is a server route rather than client-side: the browser only ever holds one page,
so a CSV built in JavaScript would quietly export a fraction of the results and look like it
exported everything.

## Validation

Rules come off the fields. **The field list is the mass-assignment allowlist** — anything the panel
does not declare never reaches the model, whatever the request contains.

A closure receives the record being edited, which is what lets a unique rule ignore its own row:

```php
['name' => 'sku', 'rules' => fn (?Product $entry) => [
    'required', Rule::unique('products', 'sku')->ignore($entry?->getKey()),
]],
```

A field with no `rules` is validated as `nullable`, so it is still saved.

## Generating a panel

```bash
php artisan gentelella:crud Product
```

Reads the table through Laravel's schema builder — no `doctrine/dbal` — and infers:

| Database | Column type | Field type |
|---|---|---|
| `tinyint(1)` / `boolean` | `boolean` | `switch` |
| `timestamp`, `datetime` | `datetime` | `datetime` |
| `date` | `date` | `date` |
| `decimal`, `float`, `double` | `money` | `number` |
| `int` | `number` | `number` |
| `text`, `json` | `text` | `textarea` |
| `*_id` matching a model | `number` | `select_from_model` |

Rules come from nullability **and defaults** — a NOT NULL column with a database default is not
required, because the database already knows what to put there.

The output is a starting point, not a contract. It is plain PHP meant to be edited.
