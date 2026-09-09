<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Console;

use ColorlibHQ\Gentelella\Database\Seeders\GentelellaDemoSeeder;
use ColorlibHQ\Gentelella\Gentelella;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Prepares the bundled demo: runs the demo migrations and seeds sample rows for
 * the CRUD-backed pages.
 */
class DemoCommand extends Command
{
    protected $signature = 'gentelella:demo {--fresh : Roll the demo tables back before migrating}';

    protected $description = 'Migrate and seed the bundled Gentelella demo data';

    /**
     * Create or update the account the sign-in screen fills in.
     *
     * Idempotent: re-running resets the password, which is what you want after
     * somebody has changed it on a public demo.
     */
    private function createDemoUser(): void
    {
        $credentials = app(Gentelella::class)->demoCredentials();

        if ($credentials === null) {
            return;
        }

        /** @var class-string<Model>|null $class */
        $class = $this->laravel['config']->get('auth.providers.users.model');

        if ($class === null || ! class_exists($class)) {
            $this->components->warn('No user model configured; skipped the demo account.');

            return;
        }

        $model = new $class;

        if (! Schema::hasTable($model->getTable())) {
            $this->components->warn("Table [{$model->getTable()}] does not exist; skipped the demo account.");

            return;
        }

        $class::query()->updateOrCreate(
            ['email' => $credentials['email']],
            ['name' => $credentials['name'], 'password' => Hash::make($credentials['password'])],
        );

        $this->components->info('Demo account ready: '.$credentials['email']);
    }

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

        $this->createDemoUser();

        return self::SUCCESS;
    }
}
