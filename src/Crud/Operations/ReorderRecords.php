<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Drag-free reordering.
 *
 * Rows move with up/down buttons rather than drag-and-drop: it needs no library,
 * works on a phone, and can be driven from the keyboard — none of which is true
 * of a bare HTML5 drag implementation.
 */
trait ReorderRecords
{
    use RendersCrudViews;

    public function reorder(): View
    {
        // The route exists on every ResourceController, so a panel that never
        // opted in answers 404 rather than raising.
        abort_unless($this->panel->isReorderable(), 404);

        return $this->crudView('gentelella::crud.reorder', [
            'panel' => $this->panel,
            'entries' => $this->panel->reorderQuery()->get(),
        ]);
    }

    public function saveReorder(Request $request): RedirectResponse
    {
        abort_unless($this->panel->isReorderable(), 404);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required'],
        ]);

        $this->panel->applyOrder($validated['order']);

        return redirect()
            ->route($this->panel->routeName().'.index')
            ->with('status', __(':entity reordered.', ['entity' => ucfirst($this->panel->getPlural())]));
    }
}
