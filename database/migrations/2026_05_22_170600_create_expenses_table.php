<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignUuid('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $table->foreignUuid('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('category', 24);
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('paid_by', 16);
            $table->foreignUuid('paid_by_user_id')->constrained('users')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'expense_date']);
            $table->index(['category', 'expense_date']);
            $table->index('car_id');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
