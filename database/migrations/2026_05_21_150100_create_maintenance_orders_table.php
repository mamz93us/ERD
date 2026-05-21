<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('car_id')->constrained('cars')->restrictOnDelete();
            $table->foreignUuid('garage_id')->constrained('garages')->restrictOnDelete();
            $table->string('order_type', 32)->default('preventive');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->unsignedInteger('odometer_at_service')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('status', 16)->default('scheduled');
            $table->string('invoice_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['car_id', 'scheduled_start', 'scheduled_end'], 'maint_orders_car_window_idx');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_orders');
    }
};
