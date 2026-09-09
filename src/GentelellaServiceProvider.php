<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella;

use ColorlibHQ\Gentelella\Auth\AuthRouteRegistrar;
use ColorlibHQ\Gentelella\Console\CrudMakeCommand;
use ColorlibHQ\Gentelella\Console\DemoCommand;
use ColorlibHQ\Gentelella\Console\InstallCommand;
use ColorlibHQ\Gentelella\Console\MakeAuthCommand;
use ColorlibHQ\Gentelella\Crud\RouteRegistrar;
use ColorlibHQ\Gentelella\Demo\Http\ProductController as DemoProductController;
use ColorlibHQ\Gentelella\Menu\MenuBuilder;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GentelellaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gentelella.php', 'gentelella');

        $this->app->singleton(MenuBuilder::class, fn ($app): MenuBuilder => new MenuBuilder(
            $app['config'],
            $app,
        ));

        $this->app->singleton(Gentelella::class, fn ($app): Gentelella => new Gentelella(
            $app->make(MenuBuilder::class),
            $app['config'],
            $app['router'],
            $app['url'],
        ));

        $this->app->alias(Gentelella::class, 'gentelella');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'gentelella');

        // Error views are not registered under the `errors` namespace on
        // purpose. Laravel's exception handler *replaces* that namespace at
        // render time with the application's own view paths plus its built-in
        // fallback, so anything a package registers there is discarded. They
        // have to be published into resources/views/errors to take effect —
        // see the gentelella-errors tag below.

        // JSON translations, so every __('…') string in the package can be
        // overridden by an application's lang/<locale>.json without publishing
        // a single view.
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        $this->registerRouteMacro();

        // Deferred until every provider has booted, which is when the
        // application's own route files have been loaded. Registering earlier
        // would mean asking Route::has() about routes that do not exist yet —
        // the package would claim /login before the app's own could, and its
        // route would match first.
        $this->app->booted(function (): void {
            $this->registerAuthRoutes();
            $this->registerDemoRoutes();
        });

        // <x-gentelella::card>, <x-gentelella::stat>, and friends. Anonymous
        // components only — there is no state to hold, and a class per
        // component would be weight the package does not need.
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'gentelella');

        // Every package view can rely on $gentelella without the layout having
        // to resolve it, and without a page remembering to pass it down.
        $this->app['view']->composer('gentelella::*', function ($view): void {
            $view->with('gentelella', $this->app->make(Gentelella::class));
        });

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/gentelella.php' => config_path('gentelella.php'),
        ], 'gentelella-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/gentelella'),
        ], 'gentelella-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path(),
        ], 'gentelella-lang');

        $this->publishes([
            __DIR__.'/../resources/views/errors' => resource_path('views/errors'),
        ], 'gentelella-errors');

        $this->commands([
            InstallCommand::class,
            CrudMakeCommand::class,
            DemoCommand::class,
            MakeAuthCommand::class,
        ]);
    }

    /**
     * The bundled login / registration / password-reset screens.
     *
     * Off by default. An application that already has auth keeps its own routes
     * and points them at the gentelella::auth.* views instead — which is why
     * every route here is skipped when one of that name already exists.
     */
    private function registerAuthRoutes(): void
    {
        if (! $this->app['config']->get('gentelella.auth.enabled', false)) {
            return;
        }

        // Laravel's `guest` middleware sends an already-authenticated visitor to
        // its own default, which knows nothing about this package. Left alone,
        // an app whose "/" points at the sign-in screen loops forever: /login
        // bounces to /, / bounces back to /login.
        //
        // The closure reads config at call time rather than closing over
        // anything: redirectUsing() stores it statically, and a captured
        // container would outlive the application that set it.
        RedirectIfAuthenticated::redirectUsing(
            fn (): string => (string) config('gentelella.auth.home', '/'),
        );

        AuthRouteRegistrar::register();
    }

    /**
     * The bundled showcase — every page from the static template.
     *
     * Off unless config('gentelella.demo') says otherwise, so a consumer app
     * ships none of it. Route names are 'gentelella.demo.<slug>', which is what
     * HrefFilter resolves the menu's `page` entries against; the slugs come
     * from the generated manifest, so the sidebar and the routes cannot drift.
     *
     * Pages marked `crud` are skipped here — their own controller registers
     * them, under the same route name.
     */
    private function registerDemoRoutes(): void
    {
        if (! $this->app['config']->get('gentelella.demo', false)) {
            return;
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        /** @var array<string, array{title: string, shell: bool, crud: bool, scripts: bool}> $pages */
        $pages = require __DIR__.'/../resources/demo-pages.php';

        $prefix = trim((string) $this->app['config']->get('gentelella.demo_prefix', 'demo'), '/');
        $middleware = $this->app['config']->get('gentelella.demo_middleware', ['web']);

        Route::middleware($middleware)->prefix($prefix)->group(function () use ($pages): void {
            foreach ($pages as $slug => $meta) {
                if ($meta['crud'] ?? false) {
                    continue;
                }

                $name = 'gentelella.demo.'.$slug;

                if (Route::has($name)) {
                    continue;
                }

                Route::get($slug, fn () => app(ViewFactory::class)->make('gentelella::demo.'.$slug))
                    ->name($name);
            }

            // The Tables page is a real CRUD panel rather than static markup, so
            // the demo exercises the engine instead of picturing it. Middleware
            // is left empty because the surrounding group already applies it.
            if (! Route::has('gentelella.demo.tables.index')) {
                RouteRegistrar::register('tables', DemoProductController::class, [
                    'as' => 'gentelella.demo.tables',
                    'middleware' => [],
                ]);
            }

            if (! Route::has('gentelella.demo')) {
                Route::get('/', fn () => redirect()->route('gentelella.demo.index'))
                    ->name('gentelella.demo');
            }
        });
    }

    /**
     * Route::gentelella('products', ProductController::class)
     *
     * The macro delegates to RouteRegistrar rather than closing over anything:
     * Laravel's macro registry is static and outlives the application that
     * registered it, so a closure holding a container breaks the next boot.
     */
    private function registerRouteMacro(): void
    {
        if (Route::hasMacro('gentelella')) {
            return;
        }

        Route::macro(
            'gentelella',
            fn (string $uri, string $controller, array $options = []) => RouteRegistrar::register($uri, $controller, $options),
        );
    }

    /** @return list<string> */
    public function provides(): array
    {
        return [Gentelella::class, MenuBuilder::class, 'gentelella'];
    }
}
