<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests\Fixtures\Controllers;

use ColorlibHQ\Gentelella\Crud\ResourceController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;

class FilteredProductController extends ResourceController
{
    protected function setup(): void
    {
        $this->panel
            ->model(Product::class)
            ->route('admin/products')
            ->columns([['name' => 'name'], ['name' => 'status']])
            ->filters([
                ['name' => 'name', 'type' => 'text'],
                ['name' => 'status', 'type' => 'select', 'options' => ['live' => 'Live', 'draft' => 'Draft']],
                ['name' => 'active', 'type' => 'boolean'],
                ['name' => 'released_at', 'type' => 'date_range'],
            ]);
    }
}
