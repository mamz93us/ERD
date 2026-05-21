<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group')->index();
            $table->string('key');
            $table->text('text_ar')->nullable();
            $table->text('text_en')->nullable();
            $table->boolean('is_system')->default(false);
            $table->foreignUuid('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['group', 'key']);
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
