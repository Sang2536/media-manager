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
            $table->string('action'); // ví dụ: create, update, delete, upload, rename...
            $table->string('target_type'); // media_file, media_folder, media_tag, ...
            $table->unsignedBigInteger('target_id')->nullable(); // ID của bản ghi bị tác động

            // Thông tin bổ sung
            $table->text('description')->nullable(); // mô tả chi tiết hành động
            $table->json('data')->nullable(); // chứa dữ liệu snapshot trước/sau nếu cần

            //  Thông tin người thao tác
            $table->ipAddress('ip')->nullable(); // IP người dùng
            $table->string('user_agent', 1024)->nullable(); // Trình duyệt/người dùng

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
