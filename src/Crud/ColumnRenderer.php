<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Turns a model + column definition into a cell of HTML.
 *
 * Each column type is a Blade view under resources/views/columns. An unknown
 * type falls back to `text` rather than raising: a typo in a column definition
 * should show the value plainly, not break the whole table.
 */
class ColumnRenderer
{
    public function __construct(private readonly ViewFactory $views) {}

    public function render(Column $column, Model $entry): string
    {
        $view = 'gentelella::columns.'.$column->type;

        if (! $this->views->exists($view)) {
            $view = 'gentelella::columns.text';
        }

        return trim($this->views->make($view, [
            'column' => $column,
            'entry' => $entry,
            'value' => $this->value($column, $entry),
        ])->render());
    }

    /**
     * The raw value behind a column, before the type view formats it.
     *
     * A `closure` column computes its own value. A relationship column reads the
     * chosen attribute off the related model, tolerating a null relation. Every
     * other column is read with data_get, so dotted names reach into casts and
     * nested arrays.
     */
    public function value(Column $column, Model $entry): mixed
    {
        if ($column->type === 'closure') {
            $callback = $column->option('value');

            return is_callable($callback) ? $callback($entry) : null;
        }

        if ($column->isRelationship()) {
            $related = $entry->getAttribute($column->name);

            return $related instanceof Model
                ? data_get($related, $column->attribute())
                : null;
        }

        return data_get($entry, $column->name);
    }
}
