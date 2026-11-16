<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests\Fixtures\Controllers;

use ColorlibHQ\Gentelella\Crud\Operations\ListRecords;
use ColorlibHQ\Gentelella\Crud\Panel;
use ColorlibHQ\Gentelella\Tests\Fixtures\Models\Product;
use Illuminate\Routing\Controller;

/**
 * Composes only the list operation, so the macro should register only the
 * routes that exist.
 */
class ReadOnlyProductController extends Controller
{
    use ListRecords;

    protected Panel $panel;

    public function __construct()
    {
        $this->panel = (new Panel)
            ->model(Product::class)
            ->route('admin/readonly')
            ->withoutActions()
            ->columns([['name' => 'name']]);
    }
}
