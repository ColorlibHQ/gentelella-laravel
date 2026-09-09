<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Contracts\View\View;

trait ShowRecord
{
    use RendersCrudViews;

    public function show(int|string $id): View
    {
        return $this->crudView('gentelella::crud.show', [
            'panel' => $this->panel,
            'entry' => $this->panel->findOrFail($id),
        ]);
    }
}
