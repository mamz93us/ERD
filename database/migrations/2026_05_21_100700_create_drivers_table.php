<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('national_id')->unique();
            $table->string('full_name');
            $table->string('full_name_ar')->nullable();
            $table->string('phone');
            $table->string('whatsapp_phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('employment_type', 16)->default('salaried');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('trip_commission_percentage', 5, 2)->default(0);
            $table->string('status', 16)->default('active');
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
