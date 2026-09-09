# Field types

Each is a Blade view under `resources/views/fields`, receiving `$field`, `$entry` and `$value`. An
unknown type falls back to `text`.

| Type | Options | |
|---|---|---|
| `text` | `attributes` | Default |
| `email`, `tel`, `url`, `password` | `attributes` | Typed inputs |
| `number` | `attributes` (e.g. `['step' => '0.01']`) | |
| `date`, `time`, `color` | | Native pickers |
| `datetime` | | `datetime-local`, formatted from a Carbon value |
| `range` | `min`, `max`, `step` | |
| `textarea` | `rows` | |
| `hidden` | | No label or wrapper |
| `select` | `options`, `allows_null`, `placeholder` | `options` is a value => label map |
| `select_from_model` | `model`, `attribute`, `scope`, `limit`, `options` | belongsTo picker |
| `radio` | `options` | |
| `checkbox` | | Single box with a hidden companion |
| `switch` | | Toggle with a hidden companion |
| `month`, `week` | | Native month and week pickers |
| `multi_select` | `options`, `model`, `attribute`, `limit` | Chips with autocomplete; submits `name[]` |
| `checklist` | `options`, `model`, `attribute`, `limit` | Checkbox set; submits `name[]` |
| `checklist_from_model` | same as `checklist` | Alias reading options from a model |
| `rich_text` | | Editor over a hidden textarea |
| `date_range` | `placeholder` | Submits `name[from]` and `name[to]` |
| `upload` | `accept`, `multiple`, `button` | Styled file input |
| `avatar` | `accept`, `initial` | Circular image picker |
| `otp` | `length` (6) | One box per digit; submits `name[]` |
| `repeatable` | `fields`, `add_label` | Repeating group; submits `name[i][sub]` |

## Common keys

| Key | |
|---|---|
| `name` | Attribute the value is read from and saved to |
| `label` | Defaults to a humanised `name` |
| `rules` | String, array, or a closure receiving the record being edited |
| `hint` | Help text under the control |
| `default` | Used when the record has no value |

A required field is detected from its rules and gets `required` plus a visible marker.

## Notes

**`checkbox` and `switch` emit a hidden input** carrying `0` before the real control. An unchecked
box sends nothing at all, so without it, unticking a box would never save.

**`select_from_model`** reads its options from the related model, ordered by the displayed
attribute and capped at `limit` (default 1000). Past that point the right control is a search field,
not a `<select>` with a hundred thousand options.

```php
['name' => 'category_id', 'label' => 'Category', 'type' => 'select_from_model',
 'model' => Category::class, 'attribute' => 'title',
 'scope' => fn ($q) => $q->where('active', true)],
```

**Old input wins over the stored value**, so a failed validation redisplays what the user typed
rather than silently reverting it.

## The composite types

Several types submit something other than a single scalar. Validate them accordingly.

**`date_range`** — the visible control is a readonly label, so the picker maintains hidden
`name[from]` and `name[to]` inputs carrying ISO dates. That is what actually submits:

```php
['name' => 'window', 'type' => 'date_range',
 'rules' => ['nullable', 'array'], ],
// and in your own request: 'window.from' => 'nullable|date'
```

**`multi_select`, `checklist`, `otp`** submit `name[]`. `checklist` also emits a hidden empty value
so that unticking everything still saves — otherwise the key vanishes from the request and the
field is left untouched.

**`repeatable`** submits `name[0][sub]`, `name[1][sub]`, … Rows are renumbered on every add and
remove, so deleting one from the middle does not leave a gap that PHP would read back as a
string-keyed array.

```php
['name' => 'items', 'type' => 'repeatable', 'fields' => [
    ['name' => 'label', 'type' => 'text'],
    ['name' => 'qty', 'type' => 'number'],
]],
```

**`rich_text`** writes its value unescaped, because the value is markup by definition. Whatever you
store there is trusted — sanitise on the way in if it is not.

**`upload` and `avatar`** need `enctype="multipart/form-data"`, which the CRUD form always sets.
Neither stores the file for you: handle the uploaded file in your controller.

## Adding your own

A field type is a single Blade view under `resources/views/fields`, receiving `$field`, `$entry` and
`$value`. Publish the views and drop one in:

```bash
php artisan vendor:publish --tag=gentelella-views
```
