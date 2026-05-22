<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('note_number')->unique();
            $table->foreignUuid('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->foreignUuid('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('issue_date');
            $table->string('reason', 32);
            $table->text('reason_details')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status', 24)->default('draft');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('applied_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('e_invoice_reference')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('invoice_id');
            $table->index('issue_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
