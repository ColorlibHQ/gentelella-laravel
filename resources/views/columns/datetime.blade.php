@include('gentelella::columns.date', [
    'column' => new \ColorlibHQ\Gentelella\Crud\Column(
        $column->name, 'date', $column->label, $column->searchable, $column->orderable,
        array_merge($column->options, ['format' => $column->option('format', 'M j, Y H:i')]),
    ),
    'entry' => $entry,
    'value' => $value,
])
