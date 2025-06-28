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
use Faker\Factory as Faker;

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
                'parent_id' => null,
                'name' => 'Root Folder - ' . $user->name,
                'path' => Str::slug('Root Folder - ' . $user->name),
                'depth' => 0,
            ]);

            $permissions = [
                'view', 'edit', 'delete', 'upload'
            ];

            $depth = 0;
            // Tạo thư mục con
            $folders = collect(['Cosplay', 'Wallpapers', 'Screenshots'])->map(function ($folderName) use ($user, $rootFolder, $depth, $permissions) {
                $depth++;
                return MediaFolder::create([
                    'user_id' => $user->id,
                    'parent_id' => $rootFolder->id,
                    'name' => $folderName,
                    'slug' => Str::slug($folderName),
                    'path' => $rootFolder->path . '/' . Str::slug($folderName),
                    'depth' => $depth,
                    'storage' => 'local',
                    'kind' => 'folder',
                    'folder_type' => array_rand([null, 'upload', 'save']),
                    'is_locked' => rand(0, 1),
                    'is_shared' => rand(0, 1),
                    'is_favorite' => rand(0, 1),
                    'comments' => $folderName . ' comments',
                    'permissions' => array_rand($permissions, rand(1, 4)),
                ]);
            });

            // Các tags có thể dùng chung
            $tags = collect(['Anime', 'Game', 'Art', 'HD', 'Portrait', 'Nature'])->map(function ($name) {
                return MediaTag::firstOrCreate([
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]);
            });

            // Với mỗi folder → tạo 3 file
            $folders->each(function ($folder) use ($user, $permissions, $tags) {
                $folder->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

                for ($i = 1; $i <= 3; $i++) {
                    $faker = Faker::create();

                    $folder_path = 'media';

                    if($folder->name == 'Cosplay') {
                        $folder_path .= '/Cosplay';

                        $file_path = $faker->randomElements([
                            '/Aqua-Cantarera-C-2.jpg',
                            '/Aqua-Cantarera-C-4.jpg',
                            '/Aqua-Cantarera-C-7.jpg',
                            '/cosplay-firefly-6.jpg',
                            '/cosplay-saber-1.jpg',
                            '/cosplay-saber-8.jpg',
                            '/cosplay-saber-12.jpg',
                            '/osplay_yasuo-lol-1.webp',
                            '/lumine-genshin-impact-1.webp',
                        ]);
                    } else if($folder->name == 'Wallpapers') {
                        $folder_path .= '/Wallpapers';

                        $file_path = $faker->randomElements([
                            '/nature-birt-in-flower-1.jpeg',
                            '/nature_forest_1.jpeg',
                        ]);
                    } else if($folder->name == 'Screenshots') {
                        $folder_path .= '/Screenshots';

                        $file_path = $faker->randomElements([
                            '/screen-images-keyboard-1.jpeg',
                            '/screenshort-iphone-1.jpeg',
                        ]);
                    } else {
                        $file_path = "/{$folder->name}/file_$i.jpg";
                    }

                    $file = MediaFile::create([
                        'user_id' => $user->id,
                        'media_folder_id' => $folder->id,
                        'filename' => ltrim($file_path[0], '/'),
                        'original_name' => $folder_path . "_original_" . $file_path[0],
                        'mime_type' => 'image/jpeg',
                        'size' => rand(100_000, 1_000_000),
                        'path' => $folder_path . $file_path[0],
                        'thumbnail_path' => "$folder_path/thumb_file_" . $file_path[0],
                        'source_url' => 'https://example.com' . $file_path[0],
                        'storage' => 'local',
                        'is_locked' => rand(0, 1),
                        'is_shared' => rand(0, 1),
                        'is_favorite' => rand(0, 1),
                        'comments' => $faker->text(rand(100, 500)),
                        'permissions' => $faker->randomElements($permissions, rand(1, 4)),
                        'last_opened_at' => $faker->dateTimeBetween('-1 month', 'now'),
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
