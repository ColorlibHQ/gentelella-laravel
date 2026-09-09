<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests;

/**
 * The demo routes are registered when the provider boots, so demo mode has to
 * be on before the application is created — not in a beforeEach.
 */
abstract class DemoTestCase extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('gentelella.demo', true);
    }
}
