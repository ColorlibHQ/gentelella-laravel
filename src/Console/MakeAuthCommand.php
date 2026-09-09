<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Copies the auth screens into the application.
 *
 * The package's own routes are enough to sign in with, but the moment you need
 * a different redirect, an extra field or a second factor you want the code in
 * your project rather than in vendor/. This is that step: controllers, views
 * and a route file you own outright.
 */
class MakeAuthCommand extends Command
{
    protected $signature = 'gentelella:make-auth
        {--only= : Limit to one step — views, controllers or routes}
        {--force : Overwrite files that already exist}';

    protected $description = 'Copy the Gentelella auth controllers, views and routes into your app';

    private const CONTROLLERS = [
        'LoginController.php',
        'RegisterController.php',
        'PasswordResetController.php',
        'RendersAuthViews.php',
    ];

    public function handle(Filesystem $files): int
    {
        $only = $this->option('only');

        if ($only !== null && ! in_array($only, ['views', 'controllers', 'routes'], true)) {
            $this->components->error("Unknown --only value [{$only}]. Expected: views, controllers, routes.");

            return self::FAILURE;
        }

        if ($only === null || $only === 'views') {
            $this->copyViews($files);
        }

        if ($only === null || $only === 'controllers') {
            $this->copyControllers($files);
        }

        if ($only === null || $only === 'routes') {
            $this->writeRoutes($files);
        }

        if ($only === null) {
            $this->newLine();
            $this->components->bulletList([
                "require base_path('routes/gentelella-auth.php') from your bootstrap/app.php or routes/web.php",
                "Set 'enabled' => false under 'auth' in config/gentelella.php so the package stops registering its own",
            ]);
            $this->newLine();
        }

        return self::SUCCESS;
    }

    private function copyViews(Filesystem $files): void
    {
        $target = resource_path('views/vendor/gentelella/auth');
        $files->ensureDirectoryExists($target);

        foreach ($files->files(__DIR__.'/../../resources/views/auth') as $file) {
            $path = $target.'/'.$file->getFilename();

            if ($files->exists($path) && ! $this->option('force')) {
                $this->components->warn('Skipped views/vendor/gentelella/auth/'.$file->getFilename());

                continue;
            }

            $files->copy($file->getPathname(), $path);
            $this->components->info('Created resources/views/vendor/gentelella/auth/'.$file->getFilename());
        }
    }

    private function copyControllers(Filesystem $files): void
    {
        $target = app_path('Http/Controllers/Auth');
        $files->ensureDirectoryExists($target);

        foreach (self::CONTROLLERS as $name) {
            $path = $target.'/'.$name;

            if ($files->exists($path) && ! $this->option('force')) {
                $this->components->warn('Skipped app/Http/Controllers/Auth/'.$name);

                continue;
            }

            // The copy has to answer to the application's namespace, and its
            // views keep pointing at the package until they are published over.
            $source = (string) $files->get(__DIR__.'/../Auth/Http/Controllers/'.$name);
            $source = str_replace(
                'namespace ColorlibHQ\\Gentelella\\Auth\\Http\\Controllers;',
                'namespace App\\Http\\Controllers\\Auth;',
                $source,
            );

            $files->put($path, $source);
            $this->components->info('Created app/Http/Controllers/Auth/'.$name);
        }
    }

    private function writeRoutes(Filesystem $files): void
    {
        $path = base_path('routes/gentelella-auth.php');

        if ($files->exists($path) && ! $this->option('force')) {
            $this->components->warn('Skipped routes/gentelella-auth.php');

            return;
        }

        $files->ensureDirectoryExists(dirname($path));
        $files->put($path, (string) $files->get(__DIR__.'/../../resources/stubs/auth-routes.stub'));

        $this->components->info('Created routes/gentelella-auth.php');
    }
}
