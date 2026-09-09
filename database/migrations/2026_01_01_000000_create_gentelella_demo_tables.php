<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables for the bundled demo.
 *
 * Loaded only while config('gentelella.demo') is on, and prefixed so an
 * application that turns the demo on for a look does not find its own tables
 * shadowed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gentelella_demo_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
        });

        Schema::create('gentelella_demo_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()
                ->constrained('gentelella_demo_categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('draft');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gentelella_demo_products');
        Schema::dropIfExists('gentelella_demo_categories');
    }
};
