<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignUuid('category_id')->constrained('car_categories')->restrictOnDelete();
            $table->string('plate')->unique();
            $table->string('vin')->nullable()->unique();
            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('color')->nullable();
            $table->string('transmission', 16)->default('auto');
            $table->string('fuel_type', 16)->default('petrol');
            $table->unsignedTinyInteger('seats')->default(5);
            $table->string('ownership_type', 16)->default('owned');
            $table->string('status', 16)->default('available');
            $table->unsignedInteger('current_odometer')->default(0);
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'branch_id']);
            $table->index('ownership_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
