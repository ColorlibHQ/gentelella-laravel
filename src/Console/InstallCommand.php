<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'gentelella:install
        {--only= : Limit to one step — config or stubs}
        {--force : Overwrite files that already exist}';

    protected $description = 'Publish the Gentelella config and Vite entry stubs';

    /**
     * Vite entry stubs, keyed by the path they are written to relative to the
     * application root.
     */
    private const STUBS = [
        'resources/js/gentelella.js' => 'gentelella.js.stub',
        'resources/css/gentelella.scss' => 'gentelella.scss.stub',
    ];

    public function handle(Filesystem $files): int
    {
        $only = $this->option('only');

        if ($only !== null && ! in_array($only, ['config', 'stubs'], true)) {
            $this->components->error("Unknown --only value [{$only}]. Expected: config, stubs.");

            return self::FAILURE;
        }

        if ($only === null || $only === 'config') {
            $this->publishConfig();
        }

        if ($only === null || $only === 'stubs') {
            $this->publishStubs($files);
        }

        if ($only === null) {
            $this->nextSteps();
        }

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->callSilently('vendor:publish', array_filter([
            '--tag' => 'gentelella-config',
            '--force' => (bool) $this->option('force'),
        ]));

        $this->components->info('Published config/gentelella.php');
    }

    private function publishStubs(Filesystem $files): void
    {
        $force = (bool) $this->option('force');

        foreach (self::STUBS as $target => $stub) {
            $path = $this->laravel->basePath($target);

            if ($files->exists($path) && ! $force) {
                $this->components->warn("Skipped {$target} (already exists — pass --force to overwrite)");

                continue;
            }

            $files->ensureDirectoryExists(dirname($path));
            $files->put($path, $files->get(__DIR__.'/../../resources/stubs/'.$stub));

            $this->components->info("Created {$target}");
        }
    }

    private function nextSteps(): void
    {
        $this->newLine();
        $this->components->bulletList([
            'npm install gentelella@^4.1 sass',
            "Add 'resources/js/gentelella.js' to the laravel() input array in vite.config.js",
            'npm run dev',
        ]);
        $this->line('  Then extend the shell from any view:');
        $this->newLine();
        $this->line("      @extends('gentelella::page')");
        $this->newLine();
    }
}
