<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUuid('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
            $table->foreignUuid('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status', 24)->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('e_invoice_reference')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('corporate_account_id');
            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
