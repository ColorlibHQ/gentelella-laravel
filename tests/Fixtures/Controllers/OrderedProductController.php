<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests\Fixtures\Controllers;

use ColorlibHQ\Gentelella\Crud\ResourceController;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;

class OrderedProductController extends ResourceController
{
    protected function setup(): void
    {
        $this->panel
            ->model(Product::class)
            ->route('admin/ordered')
            ->entity('product', 'products')
            ->reorderable('position')
            ->columns([['name' => 'name', 'searchable' => true], ['name' => 'sku']]);
    }
}
