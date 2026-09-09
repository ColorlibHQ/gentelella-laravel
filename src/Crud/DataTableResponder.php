<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * Answers DataTables' server-side protocol from an Eloquent query.
 *
 * Reads the standard request parameters (draw / start / length / search / order)
 * and replies with `{ draw, recordsTotal, recordsFiltered, data }`, where each
 * row is a list of already-rendered cells. Matches the `data-ajax` mode in
 * src/v4/tables.js.
 */
class DataTableResponder
{
    /**
     * Hard ceiling on rows per request.
     *
     * `length` arrives from the query string, and DataTables sends -1 for "all".
     * Honouring either literally lets a stranger ask for the whole table, so
     * both are clamped.
     */
    public const MAX_PAGE_LENGTH = 200;

    public function __construct(
        private readonly Panel $panel,
        private readonly ColumnRenderer $renderer,
    ) {}

    /** @return array<string, mixed> */
    public function respond(Request $request): array
    {
        $query = $this->panel->newQuery();

        // Counted before search so the UI can say "of N total".
        $total = (clone $query)->toBase()->getCountForPagination();

        $this->applySearch($query, $this->searchTerm($request));
        $this->applyFilters($query, $request);

        $filtered = (clone $query)->toBase()->getCountForPagination();

        $this->applyOrder($query, $request);

        $rows = $query
            ->skip($this->offset($request))
            ->take($this->limit($request))
            ->get();

        return [
            // Echoed back as an integer: DataTables' docs call this out, since
            // reflecting the raw value would put request data into the response.
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn (Model $entry): array => $this->renderRow($entry))->all(),
        ];
    }

    /**
     * The query the table is currently showing — search, filters and ordering
     * applied, but unpaged.
     *
     * Used by the export, which must cover the whole result set rather than the
     * page the browser happens to be holding.
     *
     * @return Builder<Model>
     */
    public function exportQuery(Request $request): Builder
    {
        $query = $this->panel->newQuery();

        $this->applySearch($query, $this->searchTerm($request));
        $this->applyFilters($query, $request);
        $this->applyOrder($query, $request);

        return $query;
    }

    /** @return list<string> */
    private function renderRow(Model $entry): array
    {
        return array_map(
            fn (Column $column): string => $this->renderer->render($column, $entry),
            $this->panel->getColumns(),
        );
    }

    private function searchTerm(Request $request): string
    {
        $search = $request->input('search');
        $term = is_array($search) ? ($search['value'] ?? '') : $request->input('search', '');

        return trim((string) $term);
    }

    /**
     * OR across every searchable column, grouped so it cannot leak out of any
     * scope the panel's query callbacks applied.
     *
     * @param  Builder<Model>  $query
     */
    private function applySearch(Builder $query, string $term): void
    {
        $columns = $this->panel->searchableColumns();

        if ($term === '' || $columns === []) {
            return;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $q) use ($columns, $like): void {
            foreach ($columns as $column) {
                if ($column->isRelationship()) {
                    $q->orWhereHas($column->name, function (Builder $related) use ($column, $like): void {
                        $this->whereLike(
                            $related,
                            $related->getModel()->qualifyColumn($column->attribute()),
                            $like,
                        );
                    });

                    continue;
                }

                $this->whereLike($q, $q->getModel()->qualifyColumn($column->name), $like, or: true);
            }
        });
    }

    /**
     * A LIKE comparison with an explicit escape character.
     *
     * The search term has its wildcards backslash-escaped, but SQLite defines no
     * default escape character for LIKE — without the ESCAPE clause a search for
     * "%" matches every row instead of the one containing a literal percent
     * sign. MySQL and Postgres accept the same clause, so it is stated rather
     * than assumed. The column name comes from the panel definition, not the
     * request, and is wrapped by the grammar.
     *
     * @param  Builder<Model>  $query
     */
    private function whereLike(Builder $query, string $column, string $like, bool $or = false): void
    {
        $sql = $query->getQuery()->getGrammar()->wrap($column)." like ? escape '\\'";

        $or
            ? $query->orWhereRaw($sql, [$like])
            : $query->whereRaw($sql, [$like]);
    }

    /**
     * Narrow by the filter bar.
     *
     * Filters are AND-ed with each other and with the search box, and only a
     * filter the panel declared is honoured — a `filters[...]` key the panel
     * does not know about is ignored rather than turned into a query.
     *
     * @param  Builder<Model>  $query
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        $values = $request->input('filters');

        if (! is_array($values)) {
            return;
        }

        foreach ($this->panel->getFilters() as $filter) {
            $value = $values[$filter->name] ?? null;

            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $this->applyFilter($query, $filter, $value);
        }
    }

    /** @param Builder<Model> $query */
    private function applyFilter(Builder $query, Filter $filter, mixed $value): void
    {
        // An `apply` closure takes over completely, which is the escape hatch
        // for anything the built-in types do not cover.
        $custom = $filter->option('apply');

        if (is_callable($custom)) {
            $custom($query, $value);

            return;
        }

        $column = $query->getModel()->qualifyColumn($filter->column());

        match ($filter->type) {
            'text' => $this->whereLike($query, $column, '%'.addcslashes((string) $value, '%_\\').'%'),
            'boolean' => $query->where($column, filter_var($value, FILTER_VALIDATE_BOOL)),
            'date_range' => $this->applyDateRange($query, $column, $value),
            default => is_array($value)
                ? $query->whereIn($column, $value)
                : $query->where($column, $value),
        };
    }

    /**
     * A from/to pair. Either half may be absent, and a half that is not a real
     * date is dropped rather than passed to the database.
     *
     * @param  Builder<Model>  $query
     */
    private function applyDateRange(Builder $query, string $column, mixed $value): void
    {
        $range = is_array($value) ? $value : ['from' => $value];

        foreach (['from' => '>=', 'to' => '<='] as $key => $operator) {
            $bound = $range[$key] ?? null;

            if (! is_string($bound) || $bound === '' || strtotime($bound) === false) {
                continue;
            }

            $query->whereDate($column, $operator, $bound);
        }
    }

    /** @param Builder<Model> $query */
    private function applyOrder(Builder $query, Request $request): void
    {
        $order = $request->input('order.0');

        if (! is_array($order)) {
            return;
        }

        $columns = $this->panel->getColumns();
        $index = (int) ($order['column'] ?? -1);

        if (! array_key_exists($index, $columns)) {
            return;
        }

        $column = $columns[$index];

        if (! $this->panel->isOrderable($column)) {
            return;
        }

        // Never interpolated from the request: anything but 'desc' is 'asc'.
        $direction = strtolower((string) ($order['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        if (! $column->isRelationship()) {
            $query->orderBy($query->getModel()->qualifyColumn($column->name), $direction);

            return;
        }

        $this->orderByRelated($query, $column, $direction);
    }

    /**
     * Order a BelongsTo column by the related attribute, using a correlated
     * subquery rather than a join — a join would need a GROUP BY to stay
     * duplicate-free, and would collide with the panel's own query callbacks.
     *
     * @param  Builder<Model>  $query
     */
    private function orderByRelated(Builder $query, Column $column, string $direction): void
    {
        $relation = $this->panel->belongsToRelation($column);

        if (! $relation instanceof BelongsTo) {
            return;
        }

        $related = $relation->getRelated();

        $query->orderBy(
            $related->newQuery()
                ->select($related->qualifyColumn($column->attribute()))
                ->whereColumn(
                    $related->qualifyColumn($relation->getOwnerKeyName()),
                    $query->getModel()->qualifyColumn($relation->getForeignKeyName()),
                )
                ->limit(1)
                ->getQuery(),
            $direction,
        );
    }

    private function offset(Request $request): int
    {
        return max(0, (int) $request->input('start', 0));
    }

    private function limit(Request $request): int
    {
        $length = (int) $request->input('length', 10);

        if ($length < 1) {
            return self::MAX_PAGE_LENGTH;
        }

        return min($length, self::MAX_PAGE_LENGTH);
    }
}
