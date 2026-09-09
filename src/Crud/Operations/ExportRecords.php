<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use ColorlibHQ\Gentelella\Crud\ColumnRenderer;
use ColorlibHQ\Gentelella\Crud\DataTableResponder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV export of the whole filtered result set.
 *
 * The browser only ever holds one page, so a client-side CSV would quietly
 * export a fraction of the results. This re-runs the same query the table is
 * showing — search, filters and ordering included — and streams it in chunks,
 * so exporting a large table does not load it all into memory.
 */
trait ExportRecords
{
    public function export(Request $request, ColumnRenderer $renderer): StreamedResponse
    {
        $panel = $this->panel;
        $responder = new DataTableResponder($panel, $renderer);
        $query = $responder->exportQuery($request);

        $columns = array_values(array_filter(
            $panel->getColumns(),
            fn ($column): bool => $column->type !== 'actions',
        ));

        $filename = $panel->getPlural().'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query, $columns, $renderer): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, array_map(fn ($c): string => $c->label, $columns));

            $query->chunk(500, function ($rows) use ($handle, $columns, $renderer): void {
                foreach ($rows as $row) {
                    fputcsv($handle, array_map(
                        // The cell views produce HTML; a CSV wants the text.
                        fn ($c): string => trim(html_entity_decode(strip_tags($renderer->render($c, $row)))),
                        $columns,
                    ));
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
