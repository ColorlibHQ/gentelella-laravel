<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait CreateRecord
{
    use RendersCrudViews;

    public function create(): View
    {
        return $this->crudView('gentelella::crud.create', [
            'panel' => $this->panel,
            'entry' => $this->panel->newModel(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->panel->validate($request);

        $entry = $this->panel->newModel();
        $entry->fill($data)->save();

        return redirect()
            ->route($this->panel->routeName().'.index')
            ->with('status', __(':entity created.', ['entity' => ucfirst($this->panel->getSingular())]));
    }
}
