<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $guarded = [];

    public $timestamps = false;
}
