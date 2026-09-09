<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait UpdateRecord
{
    use RendersCrudViews;

    public function edit(int|string $id): View
    {
        return $this->crudView('gentelella::crud.edit', [
            'panel' => $this->panel,
            'entry' => $this->panel->findOrFail($id),
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $entry = $this->panel->findOrFail($id);

        // The record's own id is handed to the validator so a `unique` rule can
        // ignore it — otherwise saving a row unchanged fails its own uniqueness.
        $entry->fill($this->panel->validate($request, $entry))->save();

        return redirect()
            ->route($this->panel->routeName().'.index')
            ->with('status', __(':entity updated.', ['entity' => ucfirst($this->panel->getSingular())]));
    }
}
