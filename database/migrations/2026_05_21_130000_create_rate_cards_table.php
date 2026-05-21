<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('car_categories')->restrictOnDelete();
            $table->foreignUuid('corporate_account_id')->nullable()->constrained('corporate_accounts')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('hourly_rate', 15, 2)->default(0);
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->decimal('weekly_rate', 15, 2)->default(0);
            $table->decimal('monthly_rate', 15, 2)->default(0);
            $table->unsignedSmallInteger('included_km_per_day')->default(0);
            $table->decimal('extra_km_rate', 15, 2)->default(0);
            $table->decimal('extra_hour_rate', 15, 2)->default(0);
            $table->decimal('driver_daily_allowance', 15, 2)->default(0);
            $table->decimal('cross_city_surcharge', 15, 2)->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'corporate_account_id', 'is_active'], 'rate_cards_category_corp_active_idx');
            $table->index(['effective_from', 'effective_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_cards');
    }
};
