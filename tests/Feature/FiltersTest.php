<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Crud\ColumnRenderer;
use ColorlibHQ\Gentelella\Crud\DataTableResponder;
use ColorlibHQ\Gentelella\Crud\Panel;
use ColorlibHQ\Gentelella\Tests\Fixtures\Controllers\FilteredProductController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->createCrudSchema();

    $this->tools = Category::create(['title' => 'Tools']);
    $paint = Category::create(['title' => 'Paint']);

    Product::create(['name' => 'Anvil', 'sku' => 'A-1', 'price' => 30, 'category_id' => $this->tools->id, 'status' => 'live', 'active' => true, 'released_at' => '2026-01-10']);
    Product::create(['name' => 'Brush', 'sku' => 'B-2', 'price' => 10, 'category_id' => $paint->id, 'status' => 'draft', 'active' => false, 'released_at' => '2026-03-20']);
    Product::create(['name' => 'Chisel', 'sku' => 'C-3', 'price' => 20, 'category_id' => $this->tools->id, 'status' => 'live', 'active' => true, 'released_at' => '2026-06-05']);
});

function filterPanel(array $filters): Panel
{
    return (new Panel)
        ->model(Product::class)
        ->columns([['name' => 'name'], ['name' => 'status']])
        ->filters($filters);
}

function filtered(Panel $panel, array $filters): array
{
    $result = (new DataTableResponder($panel, app(ColumnRenderer::class)))
        ->respond(new Request(['filters' => $filters]));

    return array_map(fn (array $row): string => strip_tags($row[0]), $result['data']);
}

it('filters by exact value', function () {
    $panel = filterPanel([['name' => 'status', 'type' => 'select']]);

    expect(filtered($panel, ['status' => 'live']))->toBe(['Anvil', 'Chisel'])
        ->and(filtered($panel, ['status' => 'draft']))->toBe(['Brush']);
});

it('filters by a partial text match', function () {
    $panel = filterPanel([['name' => 'name', 'type' => 'text']]);

    expect(filtered($panel, ['name' => 'nvi']))->toBe(['Anvil'])
        ->and(filtered($panel, ['name' => 'is']))->toBe(['Chisel']);
});

it('treats wildcards in a text filter as literals', function () {
    $panel = filterPanel([['name' => 'name', 'type' => 'text']]);

    expect(filtered($panel, ['name' => '%']))->toBe([]);
});

it('filters by boolean, accepting the strings a form sends', function () {
    $panel = filterPanel([['name' => 'active', 'type' => 'boolean']]);

    expect(filtered($panel, ['active' => '1']))->toBe(['Anvil', 'Chisel'])
        ->and(filtered($panel, ['active' => '0']))->toBe(['Brush']);
});

it('filters by a date range, with either half optional', function () {
    $panel = filterPanel([['name' => 'released_at', 'type' => 'date_range']]);

    expect(filtered($panel, ['released_at' => ['from' => '2026-03-01', 'to' => '2026-04-01']]))->toBe(['Brush'])
        ->and(filtered($panel, ['released_at' => ['from' => '2026-03-01']]))->toBe(['Brush', 'Chisel'])
        ->and(filtered($panel, ['released_at' => ['to' => '2026-02-01']]))->toBe(['Anvil']);
});

it('ignores a date bound that is not a date', function () {
    $panel = filterPanel([['name' => 'released_at', 'type' => 'date_range']]);

    expect(filtered($panel, ['released_at' => ['from' => 'not-a-date']]))
        ->toBe(['Anvil', 'Brush', 'Chisel']);
});

it('combines filters with AND', function () {
    $panel = filterPanel([
        ['name' => 'status', 'type' => 'select'],
        ['name' => 'category_id', 'type' => 'select'],
    ]);

    expect(filtered($panel, ['status' => 'live', 'category_id' => $this->tools->id]))
        ->toBe(['Anvil', 'Chisel']);
});

it('ignores a filter the panel does not declare', function () {
    // A `filters[...]` key nobody asked for must not become a query.
    $panel = filterPanel([['name' => 'status', 'type' => 'select']]);

    expect(filtered($panel, ['price' => 30, 'status' => 'live']))->toBe(['Anvil', 'Chisel']);
});

it('skips empty values so a cleared control is not a filter', function () {
    $panel = filterPanel([['name' => 'status', 'type' => 'select']]);

    expect(filtered($panel, ['status' => '']))->toBe(['Anvil', 'Brush', 'Chisel']);
});

it('filters on a different column when told to', function () {
    $panel = filterPanel([['name' => 'category', 'type' => 'select', 'column' => 'category_id']]);

    expect(filtered($panel, ['category' => $this->tools->id]))->toBe(['Anvil', 'Chisel']);
});

it('hands over to an apply closure when one is given', function () {
    $panel = filterPanel([[
        'name' => 'cheap',
        'apply' => fn ($query, $value) => $query->where('price', '<=', (float) $value),
    ]]);

    expect(filtered($panel, ['cheap' => 20]))->toBe(['Brush', 'Chisel']);
});

it('counts filtered rows separately from the total', function () {
    $panel = filterPanel([['name' => 'status', 'type' => 'select']]);

    $result = (new DataTableResponder($panel, app(ColumnRenderer::class)))
        ->respond(new Request(['filters' => ['status' => 'live']]));

    expect($result['recordsTotal'])->toBe(3)->and($result['recordsFiltered'])->toBe(2);
});

it('renders a control for each filter type', function () {
    config()->set('gentelella.menu', [['label' => 'G', 'items' => [['key' => 'p', 'text' => 'P', 'url' => '/p']]]]);

    Route::gentelella('admin/products', FilteredProductController::class);

    $html = $this->get('/admin/products')->assertOk()->getContent();

    expect($html)->toContain('data-table-filter="status"')
        ->toContain('data-table-filter="name"')
        ->toContain('data-table-filter="active"')
        ->toContain('data-table-filter-part="from"')
        ->toContain('data-table-filter-reset');
});
