<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * The definition of one CRUD screen: which model, which columns, which fields.
 *
 * Built in a controller's setup(); read by the operations and by
 * DataTableResponder. Holds no request state, so it can be constructed once and
 * reused across the operations of a single request.
 */
class Panel
{
    /** @var class-string<Model>|null */
    private ?string $model = null;

    /** @var list<Column> */
    private array $columns = [];

    /** @var list<Field> */
    private array $fields = [];

    /** @var list<Filter> */
    private array $filters = [];

    /** @var list<Closure(Builder<Model>): mixed> */
    private array $queryCallbacks = [];

    private string $route = '';

    private ?string $routeName = null;

    private string $singular = 'record';

    private string $plural = 'records';

    private bool $actions = true;

    private ?string $orderColumn = null;

    /** @param class-string<Model> $class */
    public function model(string $class): static
    {
        $this->model = $class;

        return $this;
    }

    /**
     * The URI this panel is served from, and optionally the route name it was
     * registered under.
     *
     * The name defaults to the URI with slashes turned into dots, which is what
     * Route::gentelella() names its routes by default. Pass the second argument
     * whenever the route was registered with a different `as` — otherwise the
     * views build links to a name that was never registered.
     */
    public function route(string $route, ?string $name = null): static
    {
        $this->route = trim($route, '/');
        $this->routeName = $name;

        return $this;
    }

    public function entity(string $singular, ?string $plural = null): static
    {
        $this->singular = $singular;
        $this->plural = $plural ?? $singular.'s';

        return $this;
    }

    /** @param array<string, mixed>|string $definition */
    public function column(array|string $definition): static
    {
        $this->columns[] = Column::fromArray($definition);

        return $this;
    }

    /** @param list<array<string, mixed>|string> $definitions */
    public function columns(array $definitions): static
    {
        foreach ($definitions as $definition) {
            $this->column($definition);
        }

        return $this;
    }

    /** @param array<string, mixed>|string $definition */
    public function field(array|string $definition): static
    {
        $this->fields[] = Field::fromArray($definition);

        return $this;
    }

    /** @param list<array<string, mixed>|string> $definitions */
    public function fields(array $definitions): static
    {
        foreach ($definitions as $definition) {
            $this->field($definition);
        }

        return $this;
    }

    /** @return list<Field> */
    public function getFields(): array
    {
        return $this->fields;
    }

    /** @param array<string, mixed>|string $definition */
    public function filter(array|string $definition): static
    {
        $this->filters[] = Filter::fromArray($definition);

        return $this;
    }

    /** @param list<array<string, mixed>|string> $definitions */
    public function filters(array $definitions): static
    {
        foreach ($definitions as $definition) {
            $this->filter($definition);
        }

        return $this;
    }

    /** @return list<Filter> */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Narrow the base query — a tenant scope, a soft-delete filter, an eager
     * load. Applied before search, filtering and ordering, and included in the
     * unfiltered total, so it reads as "the rows this panel is about".
     *
     * @param  Closure(Builder<Model>): mixed  $callback
     */
    public function query(Closure $callback): static
    {
        $this->queryCallbacks[] = $callback;

        return $this;
    }

    /**
     * Make the panel reorderable, storing each row's position in `$column`.
     *
     * The column has to be a real integer column on the table — reordering
     * writes to it.
     */
    public function reorderable(string $column = 'position'): static
    {
        $this->orderColumn = $column;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->orderColumn !== null;
    }

    public function orderColumn(): string
    {
        if ($this->orderColumn === null) {
            throw new \LogicException('This panel is not reorderable. Call $this->panel->reorderable() in setup().');
        }

        return $this->orderColumn;
    }

    /**
     * Every row, in its stored order.
     *
     * Reordering is a whole-list operation, so it is not paginated — a panel
     * with more rows than fit on one screen wants a different interaction.
     *
     * @return Builder<Model>
     */
    public function reorderQuery(): Builder
    {
        return $this->newQuery()->orderBy($this->orderColumn());
    }

    /**
     * Persist a new order.
     *
     * Positions are written from the submitted sequence rather than trusted
     * from the request, and the whole thing runs in a transaction so a failure
     * halfway cannot leave the list half-renumbered.
     *
     * @param  list<mixed>  $ids
     */
    public function applyOrder(array $ids): void
    {
        $model = $this->newModel();
        $column = $this->orderColumn();

        $model->getConnection()->transaction(function () use ($ids, $model, $column): void {
            foreach (array_values($ids) as $position => $id) {
                $this->newQuery()
                    ->where($model->getKeyName(), $id)
                    ->update([$column => $position + 1]);
            }
        });
    }

    /** Hide the per-row edit/delete column. */
    public function withoutActions(): static
    {
        $this->actions = false;

        return $this;
    }

    /**
     * The columns to render, with the actions column appended.
     *
     * Appended only when the panel has a route, since the buttons link to it —
     * a panel used purely as a query definition stays exactly as declared.
     *
     * @return list<Column>
     */
    public function getColumns(): array
    {
        $columns = $this->columns;

        if ($this->actions && $this->route !== '') {
            $columns[] = Column::fromArray([
                'name' => 'actions',
                'type' => 'actions',
                'label' => '',
                'orderable' => false,
                'route' => $this->routeName(),
            ]);
        }

        return $columns;
    }

    /** @return list<Column> */
    public function searchableColumns(): array
    {
        return array_values(array_filter($this->columns, fn (Column $c): bool => $c->searchable));
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getSingular(): string
    {
        return $this->singular;
    }

    public function getPlural(): string
    {
        return $this->plural;
    }

    /** @return class-string<Model> */
    public function getModel(): string
    {
        if ($this->model === null) {
            throw new \LogicException('This panel has no model. Call $this->panel->model(Foo::class) in setup().');
        }

        return $this->model;
    }

    public function newModel(): Model
    {
        $class = $this->getModel();

        return new $class;
    }

    /**
     * The base query: the model, plus every query callback, plus eager loads for
     * relationship columns so rendering a page cannot turn into N+1.
     *
     * @return Builder<Model>
     */
    public function newQuery(): Builder
    {
        $query = $this->newModel()->newQuery();

        $relations = array_values(array_unique(array_map(
            fn (Column $c): string => $c->name,
            array_filter($this->columns, fn (Column $c): bool => $c->isRelationship()),
        )));

        if ($relations !== []) {
            $query->with($relations);
        }

        foreach ($this->queryCallbacks as $callback) {
            $callback($query);
        }

        return $query;
    }

    /**
     * Whether a column can be ordered in SQL.
     *
     * A plain column is ordered by its own database column. A relationship
     * column can only be ordered when the relation is a BelongsTo — that is the
     * case where each row has exactly one related value to sort on. Ordering by
     * a to-many relation has no single well-defined value, so it is refused
     * rather than silently producing duplicate rows through a join.
     */
    /**
     * The route name this panel's operations are registered under — 'products'
     * for Route::gentelella('products', …), so '.index', '.edit' and friends
     * append to it.
     */
    public function routeName(): string
    {
        return $this->routeName ?? str_replace('/', '.', $this->route);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->newQuery()->findOrFail($id);
    }

    /**
     * Validate a request against the field rules and return only the fields this
     * panel declares.
     *
     * The field list is the mass-assignment allowlist: anything the panel does
     * not declare never reaches the model, whatever the request contains.
     *
     * @return array<string, mixed>
     */
    public function validate(Request $request, ?Model $entry = null): array
    {
        $rules = [];
        $labels = [];

        foreach ($this->fields as $field) {
            $resolved = $field->rules;

            // A closure receives the record being edited, which is what lets a
            // unique rule ignore the row it belongs to.
            if ($resolved instanceof Closure) {
                $resolved = $resolved($entry);
            }

            $rules[$field->name] = $resolved;
            $labels[$field->name] = strtolower($field->label);
        }

        $validated = $request->validate($rules, [], $labels);

        return array_intersect_key(
            $validated,
            array_flip(array_map(fn (Field $f): string => $f->name, $this->fields)),
        );
    }

    public function isOrderable(Column $column): bool
    {
        if (! $column->orderable) {
            return false;
        }

        if (! $column->isRelationship()) {
            return true;
        }

        return $this->belongsToRelation($column) instanceof BelongsTo;
    }

    public function belongsToRelation(Column $column): mixed
    {
        $model = $this->newModel();

        if (! method_exists($model, $column->name)) {
            return null;
        }

        return $model->{$column->name}();
    }
}
