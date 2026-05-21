<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->nullOnDelete();
            $table->string('type', 32)->default('individual');
            $table->string('full_name');
            $table->string('full_name_ar')->nullable();
            $table->string('phone');
            $table->string('whatsapp_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->text('address')->nullable();
            $table->string('preferred_language', 5)->default('ar');
            $table->unsignedInteger('loyalty_points')->default(0);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type']);
            $table->index(['is_blacklisted']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
