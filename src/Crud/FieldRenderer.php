<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Renders one form field.
 *
 * As with columns, each type is a Blade view and an unknown type falls back to
 * `text` — a typo should still let you edit the value.
 */
class FieldRenderer
{
    public function __construct(private readonly ViewFactory $views) {}

    public function render(Field $field, Model $entry): string
    {
        $view = 'gentelella::fields.'.$field->type;

        if (! $this->views->exists($view)) {
            $view = 'gentelella::fields.text';
        }

        return $this->views->make($view, [
            'field' => $field,
            'entry' => $entry,
            'value' => $this->value($field, $entry),
        ])->render();
    }

    /**
     * Old input wins over the stored value, so a failed validation redisplays
     * what the user typed rather than silently reverting it.
     */
    public function value(Field $field, Model $entry): mixed
    {
        return old($field->name, data_get($entry, $field->name) ?? $field->default);
    }
}
