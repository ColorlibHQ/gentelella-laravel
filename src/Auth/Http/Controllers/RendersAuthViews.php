<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Auth\Http\Controllers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

/**
 * Shared plumbing for the auth screens.
 *
 * Views resolve through the factory for the same reason the CRUD operations do:
 * a package's namespaced view is registered at boot and cannot be verified as a
 * literal view name.
 */
trait RendersAuthViews
{
    /** @param array<string, mixed> $data */
    protected function authView(string $name, array $data = []): View
    {
        return app(ViewFactory::class)->make($name, $data);
    }

    protected function home(): string
    {
        return (string) config('gentelella.auth.home', '/');
    }
}
