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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_folder_id')->nullable()->constrained('media_folders')->onDelete('set null');

            $table->string('filename'); //  tên file lưu nội bộ
            $table->string('original_name'); //  tên gốc
            $table->string('mime_type'); //  loại file
            $table->integer('size'); // tính bằng byte
            $table->string('path'); // Đường dẫn file gốc
            $table->string('thumbnail_path')->nullable(); // Thumbnail

            $table->string('source_url')->nullable(); // nguồn ảnh
            $table->string('storage')->default('local'); // nơi lưu

            $table->boolean('is_locked')->default(false);
            $table->boolean('is_shared')->default(false);
            $table->boolean('is_favorite')->default(false); // Yêu thích

            $table->text('comments')->nullable(); // comment

            $table->json('permissions')->nullable();

            $table->timestamp('last_opened_at')->nullable(); // lần mở cuối cùng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
