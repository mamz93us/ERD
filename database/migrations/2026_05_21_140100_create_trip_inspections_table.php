<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_inspections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('stage', 16);  // pickup | return
            $table->foreignUuid('inspector_user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('odometer');
            $table->string('fuel_level', 16);
            $table->json('damage_marks')->nullable();      // per §9.1 — {version, marks: [...]}
            $table->json('accessories_checklist')->nullable();
            $table->string('customer_signature_path')->nullable();
            $table->string('driver_signature_path');
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('performed_at');
            $table->timestamps();

            $table->index(['trip_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_inspections');
    }
};
