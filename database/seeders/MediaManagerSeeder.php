<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MediaFolder;
use App\Models\MediaFile;
use App\Models\MediaTag;
use App\Models\MediaMetadata;
use Illuminate\Support\Str;

class MediaManagerSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Tạo 3 users demo
        User::factory()->count(3)->create()->each(function ($user) {
            // Tạo thư mục gốc của người dùng
            $rootFolder = MediaFolder::create([
                'user_id' => $user->id,
                'name' => 'Root Folder - ' . $user->name,
                'parent_id' => null,
            ]);

            // Tạo thư mục con
            $folders = collect(['Cosplay', 'Wallpapers', 'Screenshots'])->map(function ($folderName) use ($user, $rootFolder) {
                return MediaFolder::create([
                    'user_id' => $user->id,
                    'name' => $folderName,
                    'parent_id' => $rootFolder->id,
                ]);
            });

            // Các tags có thể dùng chung
            $tags = collect(['Anime', 'Game', 'Art', 'HD', 'Portrait', 'Nature'])->map(function ($name) {
                return MediaTag::firstOrCreate(['name' => $name]);
            });

            // Với mỗi folder → tạo 3 file
            $folders->each(function ($folder) use ($user, $tags) {
                for ($i = 1; $i <= 3; $i++) {
                    $file = MediaFile::create([
                        'user_id' => $user->id,
                        'filename' => Str::slug($folder->name) . "_file_$i.jpg",
                        'original_name' => $folder->name . "_original_$i.jpg",
                        'mime_type' => 'image/jpeg',
                        'size' => rand(100_000, 1_000_000),
                        'path' => "media/{$folder->name}/file_$i.jpg",
                        'thumbnail_path' => "media/{$folder->name}/thumb_file_$i.jpg",
                        'folder_id' => $folder->id,
                        'is_public' => rand(0, 1),
                    ]);

                    // Gán 1-3 tags ngẫu nhiên
                    $file->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

                    // Gán metadata
                    $file->metadata()->createMany([
                        ['key' => 'width', 'value' => rand(800, 4000)],
                        ['key' => 'height', 'value' => rand(600, 3000)],
                        ['key' => 'camera', 'value' => 'Canon EOS ' . rand(1, 9)],
                    ]);
                }
            });
        });

        Model::reguard();
    }
}
