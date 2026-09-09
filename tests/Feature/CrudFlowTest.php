<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Controllers\ProductController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Controllers\ReadOnlyProductController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->createCrudSchema();
    Route::gentelella('admin/products', ProductController::class);
    Route::gentelella('admin/readonly', ReadOnlyProductController::class);

    $this->category = Category::create(['title' => 'Tools']);
    $this->product = Product::create([
        'name' => 'Anvil', 'sku' => 'A-1', 'price' => 30.0,
        'category_id' => $this->category->id, 'active' => true,
    ]);
});

it('registers the crud routes', function () {
    $names = collect(Route::getRoutes())->map->getName()->filter()->values();

    expect($names)->toContain(
        'admin.products.index', 'admin.products.data', 'admin.products.create',
        'admin.products.store', 'admin.products.show', 'admin.products.edit',
        'admin.products.update', 'admin.products.destroy',
    );
});

it('registers only the operations a controller actually implements', function () {
    $names = collect(Route::getRoutes())->map->getName()->filter()->values()->all();

    expect($names)->toContain('admin.readonly.index', 'admin.readonly.data')
        ->and($names)->not->toContain('admin.readonly.store')
        ->and($names)->not->toContain('admin.readonly.destroy');
});

it('puts literal segments ahead of the record wildcard', function () {
    // /admin/products/create must not be swallowed by /admin/products/{record}.
    $this->get('/admin/products/create')->assertOk()->assertSee('New product');
});

it('renders the list screen wired to the json endpoint', function () {
    $this->get('/admin/products')
        ->assertOk()
        ->assertSee('data-datatable', false)
        ->assertSee('data-ajax="'.url('/admin/products/data').'"', false)
        ->assertSee('<aside class="sidebar"', false)
        ->assertSee('New product');
});

it('marks a non-orderable column in the table head', function () {
    // The appended actions column is never orderable.
    $this->get('/admin/products')->assertOk()->assertSee('data-orderable="false"', false);
});

it('serves rows from the data endpoint', function () {
    $response = $this->getJson('/admin/products/data?draw=1&length=10');

    $response->assertOk()->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

    $row = $response->json('data.0');

    expect($row[0])->toBe('<span class="cell-strong">Anvil</span>')
        ->and($row[1])->toContain('Tools')
        ->and($row[2])->toContain('€30.00')
        ->and($row[4])->toContain('Edit');
});

it('creates a record', function () {
    $this->post('/admin/products', [
        'name' => 'Hammer', 'sku' => 'H-9', 'price' => '12.50',
        'category_id' => $this->category->id, 'active' => '1',
    ])->assertRedirect(route('admin.products.index'))->assertSessionHas('status');

    expect(Product::where('sku', 'H-9')->first())
        ->not->toBeNull()
        ->name->toBe('Hammer')
        ->active->toBeTrue();
});

it('rejects an invalid record and keeps the input', function () {
    $this->from('/admin/products/create')
        ->post('/admin/products', ['name' => '', 'sku' => 'A-1', 'price' => 'free'])
        ->assertRedirect('/admin/products/create')
        ->assertSessionHasErrors(['name', 'sku', 'price']);

    expect(Product::count())->toBe(1);
});

it('only saves fields the panel declares', function () {
    // `status` is a real column on the table but not a declared field, so a
    // request that includes it must not be able to set it.
    $this->post('/admin/products', [
        'name' => 'Wrench', 'sku' => 'W-3', 'price' => '5',
        'status' => 'live', 'id' => 999,
    ])->assertRedirect();

    $created = Product::where('sku', 'W-3')->firstOrFail();

    expect($created->status)->toBe('draft')
        ->and($created->id)->not->toBe(999);
});

it('lets a record keep its own unique value on update', function () {
    $this->put('/admin/products/'.$this->product->id, [
        'name' => 'Anvil MkII', 'sku' => 'A-1', 'price' => '31',
        'category_id' => $this->category->id, 'active' => '1',
    ])->assertRedirect(route('admin.products.index'))->assertSessionHasNoErrors();

    expect($this->product->fresh()->name)->toBe('Anvil MkII');
});

it('still rejects a unique value belonging to another record', function () {
    Product::create(['name' => 'Other', 'sku' => 'B-2', 'price' => 1]);

    $this->from('/admin/products/'.$this->product->id.'/edit')
        ->put('/admin/products/'.$this->product->id, ['name' => 'X', 'sku' => 'B-2', 'price' => '1'])
        ->assertSessionHasErrors('sku');
});

it('renders the edit form with current values and a populated select', function () {
    $this->get('/admin/products/'.$this->product->id.'/edit')
        ->assertOk()
        ->assertSee('value="Anvil"', false)
        ->assertSee('Must be unique.')
        ->assertSee('<option value="'.$this->category->id.'" selected>Tools</option>', false);
});

it('turns a switch off when the box is unticked', function () {
    // An unchecked checkbox sends nothing; the hidden companion input is what
    // makes "off" reach the server at all.
    $this->put('/admin/products/'.$this->product->id, [
        'name' => 'Anvil', 'sku' => 'A-1', 'price' => '30', 'active' => '0',
    ])->assertRedirect();

    expect($this->product->fresh()->active)->toBeFalse();
});

it('shows a single record', function () {
    $this->get('/admin/products/'.$this->product->id)
        ->assertOk()
        ->assertSee('Anvil')
        ->assertSee('€30.00');
});

it('deletes a record', function () {
    $this->delete('/admin/products/'.$this->product->id)
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status');

    expect(Product::count())->toBe(0);
});

it('404s for a record that does not exist', function () {
    $this->get('/admin/products/9999')->assertNotFound();
});
