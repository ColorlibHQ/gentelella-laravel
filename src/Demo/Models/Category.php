<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Demo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Demo data only. The table is namespaced so switching the demo on cannot
 * collide with an application's own `categories`.
 */
class Category extends Model
{
    protected $table = 'gentelella_demo_categories';

    protected $guarded = [];

    public $timestamps = false;
}
