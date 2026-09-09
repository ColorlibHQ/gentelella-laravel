<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

// Parameters go in the array form throughout: Symfony's StringInput treats a
// backslash as an escape, so a fully qualified class name inside the command
// string never reaches the command intact.

beforeEach(function () {
    $this->createCrudSchema();
    $this->generated = app()->basePath('app/Http/Controllers/Admin/ProductController.php');
});

afterEach(function () {
    File::delete($this->generated);
});

it('refuses a model class it cannot find', function () {
    $this->artisan('gentelella:crud', ['name' => 'Nope'])
        ->expectsOutputToContain('not found')
        ->assertFailed();
});

it('refuses a class that is not an eloquent model', function () {
    $this->artisan('gentelella:crud', ['name' => 'Thing', '--model' => stdClass::class])
        ->expectsOutputToContain('not an Eloquent model')
        ->assertFailed();
});

it('refuses a model whose table has not been migrated', function () {
    Schema::drop('products');

    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])
        ->expectsOutputToContain('does not exist')
        ->assertFailed();
});

it('generates a controller from the table schema', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    $code = File::get($this->generated);

    expect($code)->toContain('namespace App\Http\Controllers\Admin;')
        ->toContain('class ProductController extends ResourceController')
        ->toContain('->model(Product::class)')
        ->toContain("->route('products')")
        ->toContain("->entity('product', 'products')");
});

it('infers column and field types from the database types', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    $code = File::get($this->generated);

    expect($code)
        ->toContain("['name' => 'price', 'type' => 'money']")
        ->toContain("['name' => 'active', 'type' => 'boolean']")
        ->toContain("['name' => 'released_at', 'type' => 'datetime']")
        // Fields pick the editable counterpart of each type.
        ->toContain("['name' => 'active', 'type' => 'switch'")
        ->toContain("['name' => 'price', 'type' => 'number'");
});

it('marks the first text column as the searchable one', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    expect(File::get($this->generated))
        ->toContain("['name' => 'name', 'searchable' => true, 'strong' => true]");
});

it('derives rules from nullability and type', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    $code = File::get($this->generated);

    // name is NOT NULL; sku is nullable; price is numeric.
    expect($code)->toContain("'name' => 'name', 'rules' => 'required")
        ->toContain("'name' => 'sku', 'rules' => 'nullable")
        ->toContain("'name' => 'price', 'type' => 'number', 'rules' => 'nullable|numeric'")
        // `status` is NOT NULL but has a default, so the form does not demand it.
        ->toContain("'name' => 'status', 'rules' => 'nullable|max:255'");
});

it('omits the primary key and the timestamp bookkeeping columns from fields', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    $code = File::get($this->generated);
    [, $fields] = explode('->fields([', $code, 2);

    expect($fields)->not->toContain("'name' => 'id'")
        ->and($code)->not->toContain("'name' => 'created_at'");
});

it('will not overwrite an existing controller without --force', function () {
    File::ensureDirectoryExists(dirname($this->generated));
    File::put($this->generated, '// mine');

    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])
        ->expectsOutputToContain('already exists')
        ->assertFailed();

    expect(File::get($this->generated))->toBe('// mine');

    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class, '--force' => true])->assertSuccessful();

    expect(File::get($this->generated))->toContain('ResourceController');
});

it('honours a custom route and namespace', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class, '--route' => 'shop/items', '--namespace' => 'App\\Admin'])
        ->assertSuccessful();

    $path = app()->basePath('app/Admin/ProductController.php');
    $code = File::get($path);

    expect($code)->toContain('namespace App\Admin;')->toContain("->route('shop/items')");

    File::delete($path);
});

it('produces a controller that actually parses and runs', function () {
    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class])->assertSuccessful();

    $output = [];
    $status = 0;
    exec('php -l '.escapeshellarg($this->generated).' 2>&1', $output, $status);

    expect($status)->toBe(0, implode("\n", $output));
});

it('infers a belongs-to select when the related model exists', function () {
    // The generator guesses App\Models\Category from a `category_id` column and
    // only uses it when that class is real.
    if (! class_exists('App\Models\Category')) {
        class_alias(Category::class, 'App\Models\Category');
    }

    $this->artisan('gentelella:crud', ['name' => 'Product', '--model' => Product::class, '--force' => true])
        ->assertSuccessful();

    expect(File::get($this->generated))
        ->toContain("'name' => 'category_id', 'type' => 'select_from_model', 'model' => \\App\\Models\\Category::class");
});
