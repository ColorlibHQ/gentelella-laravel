<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Tests\Fixtures\Controllers\OrderedProductController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Controllers\ProductController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->createCrudSchema();
    Route::gentelella('admin/ordered', OrderedProductController::class);
    Route::gentelella('admin/products', ProductController::class);

    $this->a = Product::create(['name' => 'Anvil', 'sku' => 'A-1', 'price' => 30, 'position' => 1]);
    $this->b = Product::create(['name' => 'Brush', 'sku' => 'B-2', 'price' => 10, 'position' => 2]);
    $this->c = Product::create(['name' => 'Chisel', 'sku' => 'C-3', 'price' => 20, 'position' => 3]);
});

it('renders the reorder screen in stored order', function () {
    $html = $this->get('/admin/ordered/reorder')->assertOk()->getContent();

    expect(strpos($html, 'Anvil'))->toBeLessThan(strpos($html, 'Brush'))
        ->and(strpos($html, 'Brush'))->toBeLessThan(strpos($html, 'Chisel'));
});

it('persists a new order from the submitted sequence', function () {
    $this->post('/admin/ordered/reorder', ['order' => [$this->c->id, $this->a->id, $this->b->id]])
        ->assertRedirect(route('admin.ordered.index'));

    expect(Product::orderBy('position')->pluck('name')->all())
        ->toBe(['Chisel', 'Anvil', 'Brush']);
});

it('numbers positions from the sequence rather than trusting the request', function () {
    $this->post('/admin/ordered/reorder', ['order' => [$this->b->id, $this->a->id, $this->c->id]]);

    expect(Product::orderBy('position')->pluck('position')->all())->toBe([1, 2, 3]);
});

it('rejects a reorder with no order', function () {
    $this->from('/admin/ordered/reorder')
        ->post('/admin/ordered/reorder', [])
        ->assertSessionHasErrors('order');
});

it('404s the reorder screen on a panel that did not opt in', function () {
    // The route exists on every ResourceController; the panel decides.
    $this->get('/admin/products/reorder')->assertNotFound();
    $this->post('/admin/products/reorder', ['order' => [1]])->assertNotFound();
});

it('offers reorder in the list header only when the panel is reorderable', function () {
    $this->get('/admin/ordered')->assertOk()->assertSee('Reorder');
    $this->get('/admin/products')->assertOk()->assertDontSee('>Reorder<', false);
});

it('exports the whole result set as csv', function () {
    $response = $this->get('/admin/ordered/export')->assertOk();

    expect($response->headers->get('content-type'))->toContain('text/csv');

    $csv = $response->streamedContent();
    $lines = array_values(array_filter(explode("\n", trim($csv))));

    expect($lines[0])->toBe('Name,Sku')
        ->and($lines)->toHaveCount(4)
        ->and($csv)->toContain('Anvil')->toContain('Chisel');
});

it('exports what the table is showing, not everything', function () {
    // Search and filters have to survive into the export, or the file quietly
    // disagrees with the screen it came from.
    $csv = $this->get('/admin/ordered/export?search[value]=Anvil')->assertOk()->streamedContent();
    $lines = array_values(array_filter(explode("\n", trim($csv))));

    expect($lines)->toHaveCount(2)->and($csv)->toContain('Anvil')->not->toContain('Chisel');
});

it('exports in the requested order', function () {
    $csv = $this->get('/admin/ordered/export?order[0][column]=0&order[0][dir]=desc')
        ->assertOk()->streamedContent();

    expect(strpos($csv, 'Chisel'))->toBeLessThan(strpos($csv, 'Anvil'));
});

it('exports text rather than the cell markup', function () {
    $csv = $this->get('/admin/products/export')->assertOk()->streamedContent();

    expect($csv)->not->toContain('<span')->toContain('Anvil');
});

it('leaves the actions column out of the export', function () {
    $csv = $this->get('/admin/products/export')->assertOk()->streamedContent();
    $header = explode("\n", trim($csv))[0];

    expect($header)->not->toContain('Edit')->not->toContain('Delete');
});

it('points the table at the export endpoint', function () {
    $this->get('/admin/ordered')->assertOk()
        ->assertSee('data-export-url="'.url('/admin/ordered/export').'"', false);
});
