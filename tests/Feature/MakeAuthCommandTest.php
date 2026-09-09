<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function () {
    File::deleteDirectory(app_path('Http/Controllers/Auth'));
    File::deleteDirectory(resource_path('views/vendor/gentelella'));
    File::delete(base_path('routes/gentelella-auth.php'));
});

it('copies controllers under the application namespace', function () {
    $this->artisan('gentelella:make-auth', ['--only' => 'controllers'])->assertSuccessful();

    $path = app_path('Http/Controllers/Auth/LoginController.php');

    expect(File::exists($path))->toBeTrue()
        ->and(File::get($path))->toContain('namespace App\Http\Controllers\Auth;')
        ->and(File::get($path))->not->toContain('namespace ColorlibHQ');
});

it('copies every controller the routes stub references', function () {
    $this->artisan('gentelella:make-auth', ['--only' => 'controllers'])->assertSuccessful();
    $this->artisan('gentelella:make-auth', ['--only' => 'routes'])->assertSuccessful();

    $routes = File::get(base_path('routes/gentelella-auth.php'));

    foreach (['LoginController', 'RegisterController', 'PasswordResetController'] as $class) {
        expect($routes)->toContain($class)
            ->and(File::exists(app_path("Http/Controllers/Auth/{$class}.php")))->toBeTrue();
    }

    // The shared trait travels with them, or the copies do not load.
    expect(File::exists(app_path('Http/Controllers/Auth/RendersAuthViews.php')))->toBeTrue();
});

it('produces controllers and a route file that parse', function () {
    $this->artisan('gentelella:make-auth')->assertSuccessful();

    $files = array_merge(
        [base_path('routes/gentelella-auth.php')],
        array_map(fn ($f): string => $f->getPathname(), File::files(app_path('Http/Controllers/Auth'))),
    );

    foreach ($files as $file) {
        $status = 0;
        $output = [];
        exec('php -l '.escapeshellarg($file).' 2>&1', $output, $status);

        expect($status)->toBe(0, $file.': '.implode("\n", $output));
    }
});

it('copies the auth views for editing', function () {
    $this->artisan('gentelella:make-auth', ['--only' => 'views'])->assertSuccessful();

    expect(File::exists(resource_path('views/vendor/gentelella/auth/login.blade.php')))->toBeTrue();
});

it('does not overwrite without --force', function () {
    File::ensureDirectoryExists(app_path('Http/Controllers/Auth'));
    File::put(app_path('Http/Controllers/Auth/LoginController.php'), '// mine');

    $this->artisan('gentelella:make-auth', ['--only' => 'controllers'])
        ->expectsOutputToContain('Skipped')
        ->assertSuccessful();

    expect(File::get(app_path('Http/Controllers/Auth/LoginController.php')))->toBe('// mine');

    $this->artisan('gentelella:make-auth', ['--only' => 'controllers', '--force' => true])->assertSuccessful();

    expect(File::get(app_path('Http/Controllers/Auth/LoginController.php')))->toContain('namespace App\Http');
});

it('rejects an unknown --only value', function () {
    $this->artisan('gentelella:make-auth', ['--only' => 'nope'])
        ->expectsOutputToContain('Unknown --only value')
        ->assertFailed();
});
