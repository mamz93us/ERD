<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('name_ar');
            $table->string('class_code', 16);
            $table->unsignedTinyInteger('default_seats');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('class_code');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_categories');
    }
};
