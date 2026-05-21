<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('recipient');
            $table->string('channel', 20);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('origin_system', 100);
            $table->string('status', 20)
                ->default('pending');
            $table->unsignedInteger('attempts')
                ->default(0);
            $table->timestamp('processed_at')
                ->nullable();
            $table->timestamp('failed_at')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('channel');
            $table->index('origin_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
