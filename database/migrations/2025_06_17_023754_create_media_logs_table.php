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
        Schema::create('media_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Mô tả hành động
            $table->string('action');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id')->nullable();

            // Thông tin bổ sung
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->json('data')->nullable();

            //  Thông tin người thao tác
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 1024)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_logs');
    }
};
