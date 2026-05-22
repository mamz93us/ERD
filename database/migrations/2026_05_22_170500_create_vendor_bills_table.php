<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bill_number');
            $table->string('vendor_type', 24);
            $table->foreignUuid('partner_agency_id')->nullable()->constrained('partner_agencies')->nullOnDelete();
            $table->foreignUuid('garage_id')->nullable()->constrained('garages')->nullOnDelete();
            $table->date('bill_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->foreignUuid('related_car_id')->nullable()->constrained('cars')->nullOnDelete();
            $table->foreignUuid('related_sub_rental_contract_id')->nullable()->constrained('sub_rental_contracts')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vendor_type', 'status']);
            $table->index('bill_date');
            $table->index('due_date');
            $table->index('partner_agency_id');
            $table->index('garage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bills');
    }
};
