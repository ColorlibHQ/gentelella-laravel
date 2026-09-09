<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Routing\Controller;

/**
 * Base controller for a CRUD screen.
 *
 * Extend it, describe the screen in setup(), and register the routes with
 * Route::gentelella('products', ProductController::class).
 *
 * Every operation is a trait, so a controller can drop one it does not want by
 * listing only the traits it needs instead of overriding methods to 403.
 */
abstract class ResourceController extends Controller
{
    use Operations\CreateRecord;
    use Operations\DeleteRecord;
    use Operations\ExportRecords;
    use Operations\ListRecords;
    use Operations\ReorderRecords;
    use Operations\ShowRecord;
    use Operations\UpdateRecord;

    protected Panel $panel;

    public function __construct()
    {
        $this->panel = new Panel;
        $this->setup();
    }

    abstract protected function setup(): void;
}
