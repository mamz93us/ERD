<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('quotation_number')->unique();
            $table->foreignUuid('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
            $table->foreignUuid('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('pickup_at');
            $table->dateTime('dropoff_at');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->unsignedInteger('estimated_distance_km')->default(0);
            $table->foreignUuid('category_id')->constrained('car_categories')->restrictOnDelete();
            $table->foreignUuid('rate_card_id')->constrained('rate_cards')->restrictOnDelete();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('valid_until');
            $table->string('status', 16)->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['customer_id', 'status']);
            $table->index('pickup_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
