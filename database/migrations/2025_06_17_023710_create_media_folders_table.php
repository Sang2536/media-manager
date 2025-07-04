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
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('media_folders')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->nullable(); // URL nếu muốn public
            $table->string('path')->nullable();
            $table->unsignedTinyInteger('depth')->nullable(); // Độ sâu cây thư mục

            $table->string('storage')->default('local');
            $table->string('kind')->default('folder');
            $table->string('folder_type')->nullable(); // Phân loại theo mục đích

            $table->boolean('is_locked')->default(false);
            $table->boolean('is_shared')->default(false);
            $table->boolean('is_favorite')->default(false);

            $table->string('thumbnail')->nullable(); // Đại diện hình ảnh
            $table->text('comments')->nullable(); // Ghi chú mô tả cho folder

            $table->json('permissions')->nullable();

            $table->timestamp('last_opened_at')->nullable(); // lần mở cuối cùng
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};
