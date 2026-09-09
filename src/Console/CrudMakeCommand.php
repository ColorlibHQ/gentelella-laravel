<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Scaffolds a CRUD controller from a model's database schema.
 *
 * Reads the table through Laravel's own schema builder — no doctrine/dbal — and
 * maps each column to a sensible column and field type. The output is a
 * starting point, not a contract: the generated file is plain PHP meant to be
 * edited.
 *
 * @phpstan-type Col array{name: string, type: string, type_name: string, nullable: bool, has_default: bool, auto: bool}
 */
class CrudMakeCommand extends Command
{
    protected $signature = 'gentelella:crud
        {name : The model name, e.g. Product}
        {--model= : Fully qualified model class (default: App\\Models\\{name})}
        {--namespace=App\\Http\\Controllers\\Admin : Controller namespace}
        {--route= : URI the panel is served from (default: plural kebab of the name)}
        {--force : Overwrite an existing controller}';

    protected $description = 'Generate a Gentelella CRUD controller from a model';

    /** Columns that describe the row rather than belonging to it. */
    private const SKIPPED_FIELDS = ['created_at', 'updated_at', 'deleted_at', 'remember_token'];

    public function handle(Filesystem $files): int
    {
        $name = Str::studly((string) $this->argument('name'));
        $modelClass = (string) ($this->option('model') ?: 'App\\Models\\'.$name);

        if (! class_exists($modelClass)) {
            $this->components->error("Model [{$modelClass}] not found. Pass --model with the full class name.");

            return self::FAILURE;
        }

        $model = new $modelClass;

        if (! $model instanceof Model) {
            $this->components->error("[{$modelClass}] is not an Eloquent model.");

            return self::FAILURE;
        }

        $table = $model->getTable();

        if (! Schema::hasTable($table)) {
            $this->components->error("Table [{$table}] does not exist. Run your migrations first.");

            return self::FAILURE;
        }

        $columns = $this->describe($table, $model);

        $path = $this->controllerPath($name);

        if ($files->exists($path) && ! $this->option('force')) {
            $this->components->error("Controller already exists at {$path}. Pass --force to overwrite.");

            return self::FAILURE;
        }

        $files->ensureDirectoryExists(dirname($path));
        $files->put($path, $this->render($name, $modelClass, $columns));

        $this->components->info('Created '.str_replace(base_path().'/', '', $path));
        $this->newLine();
        $this->line('  Register it:');
        $this->newLine();
        $this->line(sprintf(
            "      Route::gentelella('%s', \\%s\\%sController::class);",
            $this->route($name),
            trim((string) $this->option('namespace'), '\\'),
            $name,
        ));
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Both the bare type name and the full declaration are kept: SQLite and
     * MySQL express a boolean as tinyint(1), which is indistinguishable from a
     * small integer once the precision is dropped.
     *
     * @return list<Col>
     */
    private function describe(string $table, Model $model): array
    {
        $key = $model->getKeyName();

        return array_values(array_map(
            fn (array $c): array => [
                'name' => (string) $c['name'],
                'type' => strtolower((string) ($c['type'] ?? $c['type_name'] ?? 'string')),
                'type_name' => strtolower((string) ($c['type_name'] ?? $c['type'] ?? 'string')),
                'nullable' => (bool) ($c['nullable'] ?? true),
                'has_default' => ($c['default'] ?? null) !== null,
                'auto' => (string) $c['name'] === $key || (bool) ($c['auto_increment'] ?? false),
            ],
            Schema::getColumns($table),
        ));
    }

    /** @param list<Col> $columns */
    private function render(string $name, string $modelClass, array $columns): string
    {
        $stub = (string) file_get_contents(__DIR__.'/../../resources/stubs/crud-controller.stub');

        return str_replace(
            ['{{ namespace }}', '{{ model }}', '{{ modelBasename }}', '{{ class }}',
                '{{ route }}', '{{ singular }}', '{{ plural }}', '{{ columns }}', '{{ fields }}'],
            [
                trim((string) $this->option('namespace'), '\\'),
                trim($modelClass, '\\'),
                class_basename($modelClass),
                $name.'Controller',
                $this->route($name),
                Str::lower(Str::headline($name)),
                Str::lower(Str::plural(Str::headline($name))),
                $this->renderColumns($columns),
                $this->renderFields($columns),
            ],
            $stub,
        );
    }

    /** @param list<Col> $columns */
    private function renderColumns(array $columns): string
    {
        $lines = [];
        $first = true;

        foreach ($columns as $column) {
            if ($column['auto']) {
                continue;
            }

            $parts = ["'name' => '{$column['name']}'"];
            $type = $this->columnType($column);

            if ($type !== 'text') {
                $parts[] = "'type' => '{$type}'";
            }

            // The first text-ish column is what a reader scans for, so it leads
            // and carries the search.
            if ($first && $type === 'text') {
                $parts[] = "'searchable' => true";
                $parts[] = "'strong' => true";
                $first = false;
            }

            $lines[] = '                ['.implode(', ', $parts).'],';
        }

        return implode("\n", $lines);
    }

    /** @param list<Col> $columns */
    private function renderFields(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            if ($column['auto'] || in_array($column['name'], self::SKIPPED_FIELDS, true)) {
                continue;
            }

            $parts = ["'name' => '{$column['name']}'"];
            $type = $this->fieldType($column);

            if ($type !== 'text') {
                $parts[] = "'type' => '{$type}'";
            }

            if ($type === 'select_from_model' && ($related = $this->relatedModel($column)) !== null) {
                $parts[] = "'model' => \\".$related.'::class';
            }

            $parts[] = "'rules' => '".implode('|', $this->rules($column))."'";

            $lines[] = '                ['.implode(', ', $parts).'],';
        }

        return implode("\n", $lines);
    }

    /** @param Col $column */
    private function columnType(array $column): string
    {
        return match (true) {
            $this->isBoolean($column) => 'boolean',
            str_contains($column['type'], 'timestamp'), str_contains($column['type'], 'datetime') => 'datetime',
            str_contains($column['type'], 'date') => 'date',
            $this->isDecimal($column) => 'money',
            $this->isInteger($column) => 'number',
            default => 'text',
        };
    }

    /**
     * The model a foreign key points at, guessed from the column name — only
     * returned when that class actually exists, so a `*_id` that follows no
     * convention degrades to a plain number input.
     *
     * @param  Col  $column
     * @return class-string<Model>|null
     */
    private function relatedModel(array $column): ?string
    {
        if (! str_ends_with($column['name'], '_id') || ! $this->isInteger($column)) {
            return null;
        }

        $guess = 'App\\Models\\'.Str::studly(Str::substr($column['name'], 0, -3));

        return class_exists($guess) && is_subclass_of($guess, Model::class) ? $guess : null;
    }

    /** @param Col $column */
    private function fieldType(array $column): string
    {
        if ($this->relatedModel($column) !== null) {
            return 'select_from_model';
        }

        return match (true) {
            $this->isBoolean($column) => 'switch',
            str_contains($column['type'], 'timestamp'), str_contains($column['type'], 'datetime') => 'datetime',
            str_contains($column['type'], 'date') => 'date',
            str_contains($column['type'], 'text'), str_contains($column['type'], 'json') => 'textarea',
            $this->isDecimal($column), $this->isInteger($column) => 'number',
            str_contains($column['name'], 'email') => 'email',
            str_contains($column['name'], 'password') => 'password',
            default => 'text',
        };
    }

    /**
     * @param  Col  $column
     * @return list<string>
     */
    private function rules(array $column): array
    {
        // A column with a database default does not have to be filled in — the
        // database already knows what to put there.
        $optional = $column['nullable'] || $column['has_default'];
        $rules = [$optional ? 'nullable' : 'required'];

        if ($this->isBoolean($column)) {
            $rules[] = 'boolean';
        } elseif ($this->isDecimal($column) || $this->isInteger($column)) {
            $rules[] = 'numeric';
        } elseif (str_contains($column['name'], 'email')) {
            $rules[] = 'email';
        } elseif (str_contains($column['type'], 'char')) {
            $rules[] = 'max:255';
        }

        return $rules;
    }

    /** @param Col $column */
    private function isBoolean(array $column): bool
    {
        // tinyint(1) is how both SQLite and MySQL store a boolean.
        return str_contains($column['type_name'], 'bool') || $column['type'] === 'tinyint(1)';
    }

    /** @param Col $column */
    private function isDecimal(array $column): bool
    {
        foreach (['decimal', 'float', 'double', 'numeric', 'real'] as $needle) {
            if (str_contains($column['type'], $needle)) {
                return true;
            }
        }

        return false;
    }

    /** @param Col $column */
    private function isInteger(array $column): bool
    {
        return str_contains($column['type_name'], 'int') && ! $this->isBoolean($column);
    }

    private function route(string $name): string
    {
        return (string) ($this->option('route') ?: Str::kebab(Str::plural($name)));
    }

    private function controllerPath(string $name): string
    {
        $namespace = trim((string) $this->option('namespace'), '\\');
        $relative = str_replace('App\\', 'app/', $namespace);
        $relative = str_replace('\\', '/', $relative);

        return base_path($relative.'/'.$name.'Controller.php');
    }
}
