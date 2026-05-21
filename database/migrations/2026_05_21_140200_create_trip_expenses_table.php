<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('type', 16);
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable();
            $table->boolean('reimbursed')->default(false);
            $table->text('notes')->nullable();
            $table->dateTime('incurred_at');
            $table->timestamps();

            $table->index(['trip_id', 'type']);
            $table->index('incurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_expenses');
    }
};
