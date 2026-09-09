# Column types

Each is a Blade view under `resources/views/columns`, receiving `$column`, `$entry` and `$value`.
An unknown type falls back to `text` — a typo should show the value, not break the table.

| Type | Options | |
|---|---|---|
| `text` | `strong`, `mono` | Default. Wraps in the cell helper classes |
| `number` | `decimals` | Monospaced, thousands-separated |
| `money` | `symbol`, `suffix`, `decimals` | |
| `boolean` | `labels` (`['Yes','No']`) | Green/red status pill |
| `status` | `tones`, `labels`, `default_tone` | Maps a value to a pill colour |
| `date` | `format` (`M j, Y`) | Renders `<time datetime>` |
| `datetime` | `format` (`M j, Y H:i`) | |
| `relationship` | `attribute` | Reads the attribute off the related model; `—` when null |
| `link` | `href` | Closure receiving the model, or an attribute name |
| `image` | `alt` | Lazy-loaded avatar-sized image |
| `array` | `limit` | Chips, plus a `+n` overflow chip |
| `progress` | `tone` | Thin progress bar |
| `closure` | `value`, `escape` | Computed; escaped unless `escape => false` |
| `actions` | — | Appended automatically; edit and delete buttons |

## Common keys

| Key | |
|---|---|
| `name` | Attribute, or relation name for `relationship`. Supports dot notation |
| `label` | Header text; defaults to a humanised `name` |
| `searchable` | Include in the search query. Off by default |
| `orderable` | Allow sorting. On by default |

## Notes

**`status`** falls back to `default_tone` for an unmapped value, so a new enum case renders as a
neutral pill rather than an unstyled one:

```php
['name' => 'status', 'type' => 'status',
 'tones' => ['live' => 'green', 'draft' => 'yellow'], 'default_tone' => 'blue'],
```

**`closure`** escapes by default. Set `escape => false` only for markup the application produced
itself:

```php
['name' => 'summary', 'type' => 'closure', 'value' => fn ($p) => $p->name.' ('.$p->sku.')'],
```

**`actions`** is appended automatically when the panel has a route. Each button renders only if its
route exists, so a read-only panel shows no controls that 404. Turn it off with
`$this->panel->withoutActions()`.

**Searching a relationship** uses `whereHas`, so it works for any relation type. **Ordering** one
requires a `belongsTo` — see [CRUD](crud.md#server-side-tables).
