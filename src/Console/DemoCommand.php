<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Console;

use ColorlibHQ\Gentelella\Database\Seeders\GentelellaDemoSeeder;
use Illuminate\Console\Command;

/**
 * Prepares the bundled demo: runs the demo migrations and seeds sample rows for
 * the CRUD-backed pages.
 */
class DemoCommand extends Command
{
    protected $signature = 'gentelella:demo {--fresh : Roll the demo tables back before migrating}';

    protected $description = 'Migrate and seed the bundled Gentelella demo data';

    public function handle(): int
    {
        if (! $this->laravel['config']->get('gentelella.demo', false)) {
            $this->components->error("Demo mode is off. Set 'demo' => true in config/gentelella.php first.");

            return self::FAILURE;
        }

        // --force throughout: without it both commands stop to ask for
        // confirmation when APP_ENV is production, and callSilently swallows
        // the prompt — so the demo would report success having done nothing.
        if ($this->option('fresh')) {
            $this->callSilently('migrate:rollback', ['--step' => 1, '--force' => true]);
        }

        if ($this->callSilently('migrate', ['--force' => true]) !== self::SUCCESS) {
            $this->components->error('Migration failed. Run `php artisan migrate` to see why.');

            return self::FAILURE;
        }

        $this->components->info('Demo tables migrated.');

        if ($this->callSilently('db:seed', [
            '--class' => GentelellaDemoSeeder::class,
            '--force' => true,
        ]) !== self::SUCCESS) {
            $this->components->error('Seeding failed. Run `php artisan db:seed --class='.GentelellaDemoSeeder::class.'` to see why.');

            return self::FAILURE;
        }

        $this->components->info('Demo data seeded.');

        return self::SUCCESS;
    }
}
