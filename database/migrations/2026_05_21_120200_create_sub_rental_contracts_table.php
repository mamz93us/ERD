<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_rental_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_agency_id')->constrained('partner_agencies')->restrictOnDelete();
            $table->foreignUuid('car_id')->constrained('cars')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('daily_cost', 15, 2);
            $table->unsignedSmallInteger('included_km_per_day')->nullable();
            $table->decimal('extra_km_cost', 15, 2)->nullable();
            $table->text('terms')->nullable();
            $table->string('status', 16)->default('active');
            $table->string('contract_file_path')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'status']);
            $table->index(['partner_agency_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_rental_contracts');
    }
};
