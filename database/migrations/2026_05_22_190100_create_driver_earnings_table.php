<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_earnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->foreignUuid('trip_id')->constrained('trips')->restrictOnDelete();
            $table->decimal('gross_commission', 15, 2);
            $table->json('deductions')->nullable(); // [{type: 'fine'|'advance', ref_id, amount, note}]
            $table->decimal('net_payable', 15, 2);
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->unique(['driver_id', 'trip_id']);
            $table->index(['driver_id', 'pay_period_start']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_earnings');
    }
};
