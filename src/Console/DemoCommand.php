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

        if ($this->option('fresh')) {
            $this->callSilently('migrate:rollback', ['--step' => 1]);
        }

        $this->callSilently('migrate');
        $this->components->info('Demo tables migrated.');

        $this->callSilently('db:seed', ['--class' => GentelellaDemoSeeder::class]);
        $this->components->info('Demo data seeded.');

        return self::SUCCESS;
    }
}
