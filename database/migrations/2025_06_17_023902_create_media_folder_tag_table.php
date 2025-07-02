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
        Schema::create('media_folder_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_folder_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['media_folder_id', 'media_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folder_tag');
    }
};
