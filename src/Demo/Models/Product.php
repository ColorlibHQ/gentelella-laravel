<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Demo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'gentelella_demo_products';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['price' => 'float', 'stock' => 'integer', 'active' => 'boolean'];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
