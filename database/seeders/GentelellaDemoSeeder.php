<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Database\Seeders;

use ColorlibHQ\Gentelella\Demo\Models\Category;
use ColorlibHQ\Gentelella\Demo\Models\Product;
use Illuminate\Database\Seeder;

class GentelellaDemoSeeder extends Seeder
{
    private const CATEGORIES = ['Hand tools', 'Power tools', 'Paint', 'Hardware', 'Safety'];

    private const NAMES = [
        'Anvil', 'Ball-peen hammer', 'Chisel set', 'Drill bit index', 'Extension cord',
        'File rasp', 'Grinder disc', 'Hex key set', 'Impact driver', 'Jigsaw blade',
        'Knife blade pack', 'Level 600mm', 'Mallet', 'Nail punch', 'Oil can',
        'Pry bar', 'Quick clamp', 'Ratchet set', 'Spirit level', 'Tape measure',
        'Utility knife', 'Vice grip', 'Wire brush', 'Xenon work lamp', 'Yard broom',
    ];

    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $categories = collect(self::CATEGORIES)
            ->map(fn (string $title): Category => Category::create(['title' => $title]));

        foreach (self::NAMES as $i => $name) {
            Product::create([
                'category_id' => $categories[$i % $categories->count()]->id,
                'name' => $name,
                'sku' => sprintf('SKU-%04d', $i + 1),
                'price' => round(4.5 + ($i * 7.3) % 180, 2),
                'stock' => ($i * 17) % 240,
                'status' => ['live', 'draft', 'archived'][$i % 3],
                'active' => $i % 4 !== 0,
            ]);
        }
    }
}
