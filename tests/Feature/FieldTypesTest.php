<?php

declare(strict_types=1);

use ColorlibHQ\Gentelella\Crud\Field;
use ColorlibHQ\Gentelella\Crud\FieldRenderer;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;

/** Render one field against a model instance. */
function field(array $definition, array $attributes = []): string
{
    $entry = new Product($attributes);

    return preg_replace('/\s+/', ' ', app(FieldRenderer::class)->render(Field::fromArray($definition), $entry)) ?? '';
}

it('renders the native month and week pickers', function () {
    expect(field(['name' => 'starts', 'type' => 'month']))->toContain('type="month"')
        ->and(field(['name' => 'starts', 'type' => 'week']))->toContain('type="week"');
});

it('renders a multi select over a hidden multiple select', function () {
    $html = field([
        'name' => 'tags', 'type' => 'multi_select',
        'options' => ['a' => 'Alpha', 'b' => 'Beta'],
    ], ['tags' => ['b']]);

    expect($html)->toContain('data-multi-select')
        ->toContain('name="tags[]"')
        ->toContain('multiple')
        ->toContain('<option value="b" selected>Beta</option>');
});

it('renders a rich text editor over a hidden textarea on the field name', function () {
    $html = field(['name' => 'body', 'type' => 'rich_text'], ['body' => '<p>Hello</p>']);

    expect($html)->toContain('data-rich-text')
        ->toContain('<textarea name="body" hidden>')
        // Markup by definition, so it is written through rather than escaped.
        ->toContain('<p>Hello</p>');
});

it('gives the date range picker hidden from and to inputs', function () {
    // The visible control is readonly, so without these the form submits nothing.
    $html = field(['name' => 'window', 'type' => 'date_range'], ['window' => ['from' => '2026-01-01', 'to' => '2026-02-01']]);

    expect($html)->toContain('data-date-range-name="window"')
        ->toContain('name="window[from]" value="2026-01-01"')
        ->toContain('name="window[to]" value="2026-02-01"')
        ->toContain('readonly');
});

it('renders an upload and an avatar picker', function () {
    expect(field(['name' => 'doc', 'type' => 'upload', 'accept' => '.pdf']))
        ->toContain('type="file"')->toContain('accept=".pdf"')->toContain('file-input-trigger');

    expect(field(['name' => 'photo', 'type' => 'avatar']))
        ->toContain('class="avatar-upload"')->toContain('accept="image/*"');
});

it('renders one otp box per digit', function () {
    $html = field(['name' => 'code', 'type' => 'otp', 'length' => 4], ['code' => '1234']);

    expect(substr_count($html, 'class="otp-input"'))->toBe(4)
        ->and($html)->toContain('name="code[]"')
        ->toContain('autocomplete="one-time-code"')
        ->toContain('value="1"')
        ->toContain('value="4"');
});

it('keeps a checklist submitting when everything is unticked', function () {
    // Without the hidden empty value the key vanishes from the request and
    // clearing every box would silently leave the old value in place.
    $html = field([
        'name' => 'roles', 'type' => 'checklist',
        'options' => ['a' => 'Admin', 'e' => 'Editor'],
    ], ['roles' => ['e']]);

    expect($html)->toContain('<input type="hidden" name="roles" value="">')
        ->toContain('name="roles[]"')
        ->toContain('value="e" checked');
});

it('renders a repeatable with a template row and existing rows', function () {
    $html = field([
        'name' => 'items', 'type' => 'repeatable',
        'fields' => [['name' => 'label'], ['name' => 'qty', 'type' => 'number']],
    ], ['items' => [['label' => 'Widget', 'qty' => 2]]]);

    expect($html)->toContain('data-repeatable="items"')
        ->toContain('name="items[0][label]" value="Widget"')
        ->toContain('name="items[0][qty]" value="2"')
        ->toContain('data-repeatable-template')
        ->toContain('name="items[__INDEX__][label]"')
        ->toContain('data-repeatable-add');
});

it('reads checklist and multi select options from a model', function () {
    $this->createCrudSchema();
    Category::create(['title' => 'Tools']);

    $html = field([
        'name' => 'category_ids', 'type' => 'checklist',
        'model' => Category::class,
        'attribute' => 'title',
    ]);

    expect($html)->toContain('Tools');
});

it('falls back to text for a type that does not exist', function () {
    expect(field(['name' => 'name', 'type' => 'nope'], ['name' => 'Anvil']))
        ->toContain('type="text"')->toContain('value="Anvil"');
});
