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
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('communication_id')
                ->constrained('communications')
                ->cascadeOnDelete()
                ->nullable();
            $table->string('level', 20);
            $table->text('message');
            $table->json('context')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
    }
};
