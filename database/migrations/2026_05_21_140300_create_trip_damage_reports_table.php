<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_damage_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->text('description');
            $table->json('damage_area')->nullable();
            $table->json('photos')->nullable();
            $table->decimal('repair_cost_estimate', 15, 2)->default(0);
            $table->decimal('actual_repair_cost', 15, 2)->nullable();
            $table->boolean('charged_to_customer')->default(false);
            $table->decimal('customer_charge_amount', 15, 2)->nullable();
            $table->string('status', 16)->default('reported');
            $table->timestamps();

            $table->index('status');
            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_damage_reports');
    }
};
