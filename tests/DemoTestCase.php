<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests;

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The demo routes are registered when the provider boots, so demo mode has to
 * be on before the application is created — not in a beforeEach.
 */
abstract class DemoTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('gentelella.demo', true);

        // A public demo has a way in, so the suite mirrors that.
        $app['config']->set('gentelella.auth.enabled', true);
        $app['config']->set('auth.providers.users.model', User::class);
    }
}
