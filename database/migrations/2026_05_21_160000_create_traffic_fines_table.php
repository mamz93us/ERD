<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_fines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained('cars')->restrictOnDelete();
            $table->foreignUuid('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignUuid('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->string('violation_number')->unique();
            $table->dateTime('violation_date');
            $table->string('violation_type');
            $table->string('location')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_status', 16)->default('unpaid');
            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->boolean('deducted_from_driver')->default(false);
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index('payment_status');
            $table->index('violation_date');
            $table->index(['car_id', 'violation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_fines');
    }
};
