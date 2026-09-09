<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Crud\ColumnRenderer;
use ColorlibHQ\Gentelella\Crud\DataTableResponder;
use ColorlibHQ\Gentelella\Crud\Panel;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->createCrudSchema();

    $tools = Category::create(['title' => 'Tools']);
    $paint = Category::create(['title' => 'Paint']);

    Product::create(['name' => 'Anvil', 'sku' => 'A-1', 'price' => 30.0, 'category_id' => $tools->id, 'status' => 'live']);
    Product::create(['name' => 'Brush', 'sku' => 'B-2', 'price' => 10.0, 'category_id' => $paint->id, 'status' => 'draft']);
    Product::create(['name' => 'Chisel', 'sku' => 'C-3', 'price' => 20.0, 'category_id' => $tools->id, 'status' => 'live']);
    Product::create(['name' => '50% off bundle', 'sku' => 'D-4', 'price' => 5.0, 'category_id' => null, 'status' => 'draft', 'active' => false]);
});

function productPanel(array $columns = []): Panel
{
    return (new Panel)
        ->model(Product::class)
        ->columns($columns ?: [
            ['name' => 'name', 'searchable' => true],
            ['name' => 'sku', 'searchable' => true],
            ['name' => 'price', 'type' => 'money'],
            ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title', 'searchable' => true],
        ]);
}

function respond(Panel $panel, array $params = []): array
{
    return (new DataTableResponder($panel, app(ColumnRenderer::class)))
        ->respond(new Request($params));
}

it('returns the datatables envelope', function () {
    $result = respond(productPanel(), ['draw' => '3']);

    expect($result)->toHaveKeys(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
        ->and($result['recordsTotal'])->toBe(4)
        ->and($result['recordsFiltered'])->toBe(4)
        ->and($result['data'])->toHaveCount(4);
});

it('echoes draw back as an integer', function () {
    // DataTables' docs call this out: reflecting the raw value would put
    // attacker-controlled text straight into the response.
    $result = respond(productPanel(), ['draw' => '1<script>alert(1)</script>']);

    expect($result['draw'])->toBe(1)->toBeInt();
});

it('pages with start and length', function () {
    $result = respond(productPanel(), ['start' => 1, 'length' => 2]);

    expect($result['data'])->toHaveCount(2)
        ->and($result['recordsTotal'])->toBe(4);
});

it('clamps the page length so a request cannot ask for the whole table', function () {
    expect(respond(productPanel(), ['length' => 100000])['data'])
        ->toHaveCount(4);

    // -1 is DataTables' "all"; it must still be bounded.
    $responder = new ReflectionMethod(DataTableResponder::class, 'limit');
    $instance = new DataTableResponder(productPanel(), app(ColumnRenderer::class));

    expect($responder->invoke($instance, new Request(['length' => -1])))
        ->toBe(DataTableResponder::MAX_PAGE_LENGTH)
        ->and($responder->invoke($instance, new Request(['length' => 100000])))
        ->toBe(DataTableResponder::MAX_PAGE_LENGTH);
});

it('never uses a negative offset', function () {
    expect(respond(productPanel(), ['start' => -5])['data'])->toHaveCount(4);
});

it('searches only the columns marked searchable', function () {
    $result = respond(productPanel(), ['search' => ['value' => 'Anvil']]);

    expect($result['recordsFiltered'])->toBe(1)
        ->and($result['recordsTotal'])->toBe(4);

    // `status` is not a declared column at all, so its values are not searchable.
    expect(respond(productPanel(), ['search' => ['value' => 'draft']])['recordsFiltered'])->toBe(0);
});

it('treats LIKE wildcards in the search term as literals', function () {
    // Without escaping, "%" matches everything and the search silently returns
    // the whole table instead of the one product whose name contains it.
    expect(respond(productPanel(), ['search' => ['value' => '%']])['recordsFiltered'])->toBe(1);
    expect(respond(productPanel(), ['search' => ['value' => '50%']])['recordsFiltered'])->toBe(1);
    expect(respond(productPanel(), ['search' => ['value' => '_']])['recordsFiltered'])->toBe(0);
});

it('searches through a relationship column', function () {
    expect(respond(productPanel(), ['search' => ['value' => 'Paint']])['recordsFiltered'])->toBe(1);
    expect(respond(productPanel(), ['search' => ['value' => 'Tools']])['recordsFiltered'])->toBe(2);
});

it('accepts a plain search string as well as the array form', function () {
    expect(respond(productPanel(), ['search' => 'Brush'])['recordsFiltered'])->toBe(1);
});

it('orders by a plain column in both directions', function () {
    $panel = productPanel([['name' => 'name'], ['name' => 'price']]);

    $asc = respond($panel, ['order' => [['column' => 0, 'dir' => 'asc']]]);
    $desc = respond($panel, ['order' => [['column' => 0, 'dir' => 'desc']]]);

    expect($asc['data'][0][0])->toContain('50% off bundle')
        ->and($desc['data'][0][0])->toContain('Chisel');
});

it('falls back to ascending for a direction it does not recognise', function () {
    $panel = productPanel([['name' => 'name']]);

    $result = respond($panel, ['order' => [['column' => 0, 'dir' => 'desc; drop table products']]]);

    expect($result['data'][0][0])->toContain('50% off bundle')
        ->and(Product::count())->toBe(4);
});

it('ignores ordering by a column that is not orderable or does not exist', function () {
    $panel = productPanel([['name' => 'name', 'orderable' => false], ['name' => 'price']]);

    $unordered = respond($panel)['data'];
    $attempted = respond($panel, ['order' => [['column' => 0, 'dir' => 'desc']]])['data'];

    expect($attempted)->toBe($unordered);

    expect(respond($panel, ['order' => [['column' => 99, 'dir' => 'desc']]])['data'])->toBe($unordered);
});

it('orders by a belongsTo relationship attribute', function () {
    $panel = productPanel([
        ['name' => 'name'],
        ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title'],
    ]);

    $asc = respond($panel, ['order' => [['column' => 1, 'dir' => 'asc']]]);
    $titles = array_map(fn (array $row): string => strip_tags($row[1]), $asc['data']);

    // Null category sorts first in SQLite, then Paint, then the two Tools rows.
    expect($titles[1])->toBe('Paint')
        ->and(array_slice($titles, 2))->toBe(['Tools', 'Tools']);
});

it('refuses to order by a to-many relationship', function () {
    $panel = productPanel([
        ['name' => 'name'],
        ['name' => 'reviews', 'type' => 'relationship', 'attribute' => 'body'],
    ]);

    $unordered = respond($panel)['data'];

    expect(respond($panel, ['order' => [['column' => 1, 'dir' => 'desc']]])['data'])
        ->toBe($unordered);
});

it('applies panel query callbacks to the unfiltered total', function () {
    $panel = productPanel()->query(fn ($q) => $q->where('active', true));

    $result = respond($panel);

    expect($result['recordsTotal'])->toBe(3)
        ->and($result['recordsFiltered'])->toBe(3);
});

it('eager loads relationship columns instead of querying per row', function () {
    $panel = productPanel();

    DB::enableQueryLog();
    respond($panel);
    $queries = count(DB::getQueryLog());
    DB::disableQueryLog();

    // count, count, select products, select categories — not one per row.
    expect($queries)->toBeLessThanOrEqual(5);
});

it('renders each cell through its column type view', function () {
    $panel = productPanel([
        ['name' => 'name', 'strong' => true],
        ['name' => 'price', 'type' => 'money', 'symbol' => '€'],
        ['name' => 'active', 'type' => 'boolean'],
        ['name' => 'status', 'type' => 'status', 'tones' => ['live' => 'green', 'draft' => 'yellow']],
    ]);

    $row = respond($panel, ['order' => [['column' => 0, 'dir' => 'asc']]])['data'][1];

    expect($row[0])->toBe('<span class="cell-strong">Anvil</span>')
        ->and($row[1])->toContain('€30.00')
        ->and($row[2])->toContain('status-green')
        ->and($row[3])->toContain('status-green')
        ->and($row[3])->toContain('live');
});

it('renders a null relationship as a dash rather than an empty cell', function () {
    $panel = productPanel([['name' => 'category', 'type' => 'relationship', 'attribute' => 'title']]);

    $rows = respond($panel)['data'];
    $cells = array_map(fn (array $r): string => $r[0], $rows);

    expect($cells)->toContain('<span class="cell-muted">—</span>');
});

it('computes a closure column and escapes it by default', function () {
    $panel = productPanel([
        ['name' => 'shout', 'type' => 'closure', 'value' => fn (Product $p): string => '<b>'.$p->name.'</b>'],
    ]);

    expect(respond($panel)['data'][0][0])->toBe('&lt;b&gt;Anvil&lt;/b&gt;');

    $raw = productPanel([
        ['name' => 'shout', 'type' => 'closure', 'escape' => false, 'value' => fn (Product $p): string => '<b>ok</b>'],
    ]);

    expect(respond($raw)['data'][0][0])->toBe('<b>ok</b>');
});

it('falls back to the text view for an unknown column type', function () {
    $panel = productPanel([['name' => 'name', 'type' => 'not-a-real-type']]);

    expect(respond($panel)['data'][0][0])->toBe('Anvil');
});
