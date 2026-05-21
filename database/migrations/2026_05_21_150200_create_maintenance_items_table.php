<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('maintenance_order_id')->constrained('maintenance_orders')->cascadeOnDelete();
            $table->string('item_type', 16);
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->timestamps();

            $table->index('maintenance_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_items');
    }
};
