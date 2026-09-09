<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests;

use ColorlibHQ\Gentelella\GentelellaServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // No Vite manifest in a Testbench app; the layout's @vite tag would
        // otherwise throw before any markup is produced.
        $this->withoutVite();

        $this->app['view']->addLocation(__DIR__.'/fixtures/views');

        // ShareErrorsFromSession does this on every web request. Views rendered
        // directly in a test have no request behind them, and @error would
        // otherwise fail on an undefined $errors.
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    /** @return list<class-string> */
    protected function getPackageProviders($app): array
    {
        return [GentelellaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        // The web middleware group encrypts the session cookie, so the CRUD
        // routes need a key once the route macro started applying middleware.
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('session.driver', 'array');

        // Pinned so the suite does not depend on whether a .env happens to
        // exist in the testbench skeleton. Laravel's default cache store is the
        // database, and the login throttle would then query a `cache` table
        // that no migration in this package creates.
        $app['config']->set('cache.default', 'array');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** Schema for the CRUD fixtures. Only created by tests that ask for it. */
    protected function createCrudSchema(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->float('price')->default(0);
            $table->boolean('active')->default(true);
            $table->string('status')->default('draft');
            $table->timestamp('released_at')->nullable();
            $table->unsignedInteger('position')->default(0);
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('body');
        });
    }
}
