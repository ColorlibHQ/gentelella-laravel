<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Demo\Http;

use ColorlibHQ\Gentelella\Crud\ResourceController;
use ColorlibHQ\Gentelella\Demo\Models\Category;
use ColorlibHQ\Gentelella\Demo\Models\Product;
use Illuminate\Validation\Rule;

/**
 * The demo's working CRUD screen — the "Tables" page, backed by a real table
 * instead of static markup, so the demo shows the engine rather than a picture
 * of it.
 */
class ProductController extends ResourceController
{
    protected function setup(): void
    {
        $this->panel
            ->model(Product::class)
            // Registered under the demo's route namespace, so the panel is told
            // the name rather than inferring 'tables' from the URI.
            ->route('tables', 'gentelella.demo.tables')
            ->entity('product', 'products')
            ->columns([
                ['name' => 'name', 'searchable' => true, 'strong' => true],
                ['name' => 'sku', 'searchable' => true, 'mono' => true],
                ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title', 'searchable' => true],
                ['name' => 'price', 'type' => 'money', 'symbol' => '€'],
                ['name' => 'stock', 'type' => 'number'],
                [
                    'name' => 'status',
                    'type' => 'status',
                    'tones' => ['live' => 'green', 'draft' => 'yellow', 'archived' => 'red'],
                ],
                ['name' => 'active', 'type' => 'boolean'],
            ])
            ->filters([
                ['name' => 'category_id', 'label' => 'Category', 'type' => 'select',
                    'model' => Category::class, 'attribute' => 'title'],
                ['name' => 'status', 'type' => 'select',
                    'options' => ['draft' => 'Draft', 'live' => 'Live', 'archived' => 'Archived']],
                ['name' => 'active', 'type' => 'boolean'],
            ])
            ->fields([
                ['name' => 'name', 'rules' => 'required|max:120'],
                [
                    'name' => 'sku',
                    'hint' => 'Unique stock keeping unit.',
                    'rules' => fn (?Product $entry) => [
                        'required',
                        Rule::unique('gentelella_demo_products', 'sku')->ignore($entry?->getKey()),
                    ],
                ],
                [
                    'name' => 'category_id',
                    'label' => 'Category',
                    'type' => 'select_from_model',
                    'model' => Category::class,
                    'attribute' => 'title',
                ],
                ['name' => 'price', 'type' => 'number', 'rules' => 'required|numeric|min:0', 'attributes' => ['step' => '0.01']],
                ['name' => 'stock', 'type' => 'number', 'rules' => 'required|integer|min:0'],
                [
                    'name' => 'status',
                    'type' => 'select',
                    'options' => ['draft' => 'Draft', 'live' => 'Live', 'archived' => 'Archived'],
                    'rules' => 'required|in:draft,live,archived',
                ],
                ['name' => 'active', 'type' => 'switch'],
            ]);
    }
}
