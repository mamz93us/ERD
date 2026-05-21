<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 16);
            $table->string('direction', 16);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->string('external_message_id')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['customer_id', 'sent_at']);
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_communications');
    }
};
