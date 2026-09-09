# Commands

## `gentelella:install`

Publishes the config and the Vite entry stubs.

| Option | |
|---|---|
| `--only=config\|stubs` | Run one step |
| `--force` | Overwrite existing files |

Existing files are skipped rather than overwritten, so re-running after an upgrade is safe.

## `gentelella:crud {name}`

Generates a CRUD controller from a model's table.

| Option | |
|---|---|
| `--model=` | Fully qualified model class (default `App\Models\{name}`) |
| `--namespace=` | Controller namespace (default `App\Http\Controllers\Admin`) |
| `--route=` | URI the panel is served from (default plural kebab of the name) |
| `--force` | Overwrite an existing controller |

Schema is read through Laravel's own schema builder — no `doctrine/dbal`. See
[CRUD](crud.md#generating-a-panel) for what it infers.

## `gentelella:make-auth`

Copies the auth controllers, views and a route file into your application.

| Option | |
|---|---|
| `--only=views\|controllers\|routes` | Run one step |
| `--force` | Overwrite existing files |

See [Authentication](authentication.md#owning-the-code).

## Publish tags

```bash
php artisan vendor:publish --tag=gentelella-config    # config/gentelella.php
php artisan vendor:publish --tag=gentelella-views     # every view, for editing
php artisan vendor:publish --tag=gentelella-lang      # lang/en.json
php artisan vendor:publish --tag=gentelella-errors    # resources/views/errors
```

`gentelella-errors` is not optional if you want the package's error pages — see
[Errors & localisation](errors-and-localisation.md).

## `gentelella:demo`

Migrates and seeds the bundled demo data. Refuses to run unless `gentelella.demo` is on.

| Option | |
|---|---|
| `--fresh` | Roll the demo tables back before migrating |
