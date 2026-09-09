<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Database\Seeders\GentelellaDemoSeeder;
use ColorlibHQ\Gentelella\Demo\Models\Category;
use ColorlibHQ\Gentelella\Demo\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->artisan('migrate')->run();
});

it('loads the demo migrations only in demo mode', function () {
    expect(Schema::hasTable('gentelella_demo_products'))->toBeTrue()
        ->and(Schema::hasTable('gentelella_demo_categories'))->toBeTrue();
});

it('namespaces the demo tables so they cannot shadow an application table', function () {
    expect(Schema::hasTable('products'))->toBeFalse();
});

it('registers the tables page as a crud panel', function () {
    expect(Route::has('gentelella.demo.tables.index'))->toBeTrue()
        ->and(Route::has('gentelella.demo.tables.data'))->toBeTrue()
        ->and(Route::has('gentelella.demo.tables.create'))->toBeTrue();
});

it('resolves the sidebar link through the .index fallback', function () {
    // NAV carries page => 'tables'; the CRUD panel registers .index, not the
    // bare name, so HrefFilter has to fall through to it.
    $html = $this->get(route('gentelella.demo.index'))->assertOk()->getContent();

    expect($html)->toContain('href="'.url('/demo/tables').'"')
        ->and($html)->not->toContain('>Tables</span></a>#');
});

it('renders the crud list inside the demo shell', function () {
    $this->get('/demo/tables')
        ->assertOk()
        ->assertSee('<aside class="sidebar"', false)
        ->assertSee('data-ajax="'.url('/demo/tables/data').'"', false)
        ->assertSee('New product');
});

it('serves seeded rows from the data endpoint', function () {
    (new GentelellaDemoSeeder)->run();

    $response = $this->getJson('/demo/tables/data?draw=1&length=10')->assertOk();

    expect($response->json('recordsTotal'))->toBe(25)
        ->and($response->json('data'))->toHaveCount(10)
        ->and($response->json('data.0.0'))->toContain('cell-strong');
});

it('searches the seeded catalogue by name, sku and category', function () {
    (new GentelellaDemoSeeder)->run();

    expect($this->getJson('/demo/tables/data?search[value]=Anvil')->json('recordsFiltered'))->toBe(1)
        ->and($this->getJson('/demo/tables/data?search[value]=SKU-0002')->json('recordsFiltered'))->toBe(1)
        ->and($this->getJson('/demo/tables/data?search[value]=Paint')->json('recordsFiltered'))->toBe(5);
});

it('creates a product through the demo panel', function () {
    $category = Category::create(['title' => 'Hand tools']);

    $this->post('/demo/tables', [
        'name' => 'Test hammer', 'sku' => 'SKU-9999', 'category_id' => $category->id,
        'price' => '19.95', 'stock' => '4', 'status' => 'live', 'active' => '1',
    ])->assertRedirect(route('gentelella.demo.tables.index'));

    expect(Product::where('sku', 'SKU-9999')->first())->not->toBeNull();
});

it('rejects a duplicate sku', function () {
    (new GentelellaDemoSeeder)->run();

    $this->from('/demo/tables/create')
        ->post('/demo/tables', ['name' => 'Dupe', 'sku' => 'SKU-0001', 'price' => '1', 'stock' => '1', 'status' => 'draft'])
        ->assertSessionHasErrors('sku');
});

it('seeds only once', function () {
    (new GentelellaDemoSeeder)->run();
    (new GentelellaDemoSeeder)->run();

    expect(Product::count())->toBe(25);
});

it('refuses to prepare the demo when demo mode is off', function () {
    config()->set('gentelella.demo', false);

    $this->artisan('gentelella:demo')
        ->expectsOutputToContain('Demo mode is off')
        ->assertFailed();
});

it('migrates and seeds through the demo command', function () {
    $this->artisan('gentelella:demo')->assertSuccessful();

    expect(Product::count())->toBe(25);
});

it('seeds even when the environment is production', function () {
    // Both migrate and db:seed stop to ask for confirmation in production, and
    // callSilently swallows the prompt — the command reported success having
    // written nothing.
    app()['env'] = 'production';

    $this->artisan('gentelella:demo')->assertSuccessful();

    expect(Product::count())->toBe(25);
});
