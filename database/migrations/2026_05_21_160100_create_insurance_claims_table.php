<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained('cars')->restrictOnDelete();
            $table->foreignUuid('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->string('claim_number')->unique();
            $table->dateTime('incident_date');
            $table->string('incident_location')->nullable();
            $table->text('description');
            $table->string('police_report_number')->nullable();
            $table->decimal('claim_amount', 15, 2);
            $table->decimal('payout_amount', 15, 2)->nullable();
            $table->string('status', 16)->default('reported');
            $table->json('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('incident_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
