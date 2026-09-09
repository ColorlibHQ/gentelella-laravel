<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud\Operations;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

/**
 * Resolves the package's CRUD views through the view factory.
 *
 * The `view()` helper would do the same job, but its return type is narrowed to
 * views static analysis can resolve on disk, and a package's namespaced views
 * ('gentelella::crud.list') are only registered once the provider boots. Going
 * through the factory keeps the call honest instead of asserting a name the
 * analyser cannot check.
 */
trait RendersCrudViews
{
    /** @param array<string, mixed> $data */
    protected function crudView(string $name, array $data = []): View
    {
        return app(ViewFactory::class)->make($name, $data);
    }
}
