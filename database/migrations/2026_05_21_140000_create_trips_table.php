<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('trip_number')->unique();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignUuid('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
            $table->foreignUuid('car_id')->constrained('cars')->restrictOnDelete();
            $table->foreignUuid('driver_id')->constrained('drivers')->restrictOnDelete(); // chauffeur model — NOT NULL
            $table->foreignUuid('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignUuid('rate_card_id')->constrained('rate_cards')->restrictOnDelete();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->unsignedInteger('start_odometer')->nullable();
            $table->unsignedInteger('end_odometer')->nullable();
            $table->string('status', 16)->default('draft');
            $table->text('cancellation_reason')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['car_id', 'scheduled_start', 'scheduled_end'], 'trips_car_window_idx');
            $table->index(['driver_id', 'scheduled_start', 'scheduled_end'], 'trips_driver_window_idx');
            $table->index('status');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
