<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use ColorlibHQ\Gentelella\Crud\ColumnRenderer;
use ColorlibHQ\Gentelella\Crud\DataTableResponder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The list screen and the JSON endpoint that feeds it.
 *
 * index() renders the shell and an empty table; every row after that comes from
 * data(), so the first paint never waits on the query.
 */
trait ListRecords
{
    use RendersCrudViews;

    public function index(): View
    {
        return $this->crudView('gentelella::crud.list', [
            'panel' => $this->panel,
        ]);
    }

    public function data(Request $request, ColumnRenderer $renderer): JsonResponse
    {
        return response()->json(
            (new DataTableResponder($this->panel, $renderer))->respond($request),
        );
    }
}
