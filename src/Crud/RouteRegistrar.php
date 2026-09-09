<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Support\Facades\Route;

/**
 * Registers the routes for one CRUD panel.
 *
 * Deliberately stateless and static. Laravel keeps macros in a static registry
 * that outlives the application instance that registered them, so a macro
 * closing over a service provider — or over anything holding a container —
 * keeps a dead application alive and fails the moment a second one is booted.
 */
class RouteRegistrar
{
    /**
     * Route key => [verb, path suffix, controller method].
     *
     * Order matters: the literal segments must be registered before the
     * {record} wildcard, or /create is swallowed by /{record}.
     */
    private const ROUTES = [
        'data' => ['get', '/data', 'data'],
        'export' => ['get', '/export', 'export'],
        'reorder' => ['get', '/reorder', 'reorder'],
        'saveReorder' => ['post', '/reorder', 'saveReorder'],
        'create' => ['get', '/create', 'create'],
        'index' => ['get', '', 'index'],
        'store' => ['post', '', 'store'],
        'show' => ['get', '/{record}', 'show'],
        'edit' => ['get', '/{record}/edit', 'edit'],
        'update' => ['put', '/{record}', 'update'],
        'destroy' => ['delete', '/{record}', 'destroy'],
    ];

    /**
     * Only an operation the controller actually implements gets a route, so a
     * controller that omits the DeleteRecord trait has no delete route rather
     * than one that 500s on dispatch.
     *
     * @param  class-string  $controller
     * @param  array{as?: string, only?: list<string>, except?: list<string>, middleware?: mixed}  $options
     */
    public static function register(string $uri, string $controller, array $options = []): void
    {
        $uri = trim($uri, '/');
        $name = $options['as'] ?? str_replace('/', '.', $uri);
        $middleware = $options['middleware'] ?? config('gentelella.middleware', ['web']);

        $only = $options['only'] ?? array_keys(self::ROUTES);
        $except = $options['except'] ?? [];

        foreach (self::ROUTES as $key => [$verb, $suffix, $method]) {
            if (! in_array($key, $only, true) || in_array($key, $except, true)) {
                continue;
            }

            if (! method_exists($controller, $method)) {
                continue;
            }

            Route::{$verb}($uri.$suffix, [$controller, $method])
                ->middleware($middleware)
                ->name($name.'.'.$key);
        }
    }
}
