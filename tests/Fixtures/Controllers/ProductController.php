<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests\Fixtures\Controllers;

use ColorlibHQ\Gentelella\Crud\ResourceController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Category;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Validation\Rule;

class ProductController extends ResourceController
{
    protected function setup(): void
    {
        $this->panel
            ->model(Product::class)
            ->route('admin/products')
            ->entity('product', 'products')
            ->columns([
                ['name' => 'name', 'searchable' => true, 'strong' => true],
                ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title'],
                ['name' => 'price', 'type' => 'money', 'symbol' => '€'],
                ['name' => 'active', 'type' => 'boolean'],
            ])
            ->fields([
                ['name' => 'name', 'rules' => 'required|max:255'],
                [
                    'name' => 'sku',
                    'hint' => 'Must be unique.',
                    'rules' => fn (?Product $entry) => [
                        'required',
                        Rule::unique('products', 'sku')->ignore($entry?->getKey()),
                    ],
                ],
                ['name' => 'category_id', 'type' => 'select_from_model', 'model' => Category::class, 'attribute' => 'title'],
                ['name' => 'price', 'type' => 'number', 'rules' => 'required|numeric|min:0'],
                ['name' => 'active', 'type' => 'switch'],
            ]);
    }
}
