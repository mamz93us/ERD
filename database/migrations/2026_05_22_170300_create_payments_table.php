<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();
            $table->foreignUuid('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
            $table->string('method', 24);
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->foreignUuid('received_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('corporate_account_id');
            $table->index('payment_date');
            $table->index(['branch_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
