<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained('cars')->cascadeOnDelete();
            $table->string('service_type', 32);
            $table->unsignedInteger('interval_km')->nullable();
            $table->unsignedSmallInteger('interval_days')->nullable();
            $table->unsignedInteger('last_done_km')->nullable();
            $table->date('last_done_date')->nullable();
            $table->unsignedInteger('next_due_km')->nullable();
            $table->date('next_due_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['car_id', 'service_type']);
            $table->index('next_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
