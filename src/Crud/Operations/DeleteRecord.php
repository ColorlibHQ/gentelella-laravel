<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Http\RedirectResponse;

trait DeleteRecord
{
    public function destroy(int|string $id): RedirectResponse
    {
        $this->panel->findOrFail($id)->delete();

        return redirect()
            ->route($this->panel->routeName().'.index')
            ->with('status', __(':entity deleted.', ['entity' => ucfirst($this->panel->getSingular())]));
    }
}
