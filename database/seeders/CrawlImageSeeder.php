<?php

namespace Database\Seeders;

use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Helpers\MediaCrawlHelper;

class CrawlImageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::count();

        if (! $users) {
            //  Tạo Use và thư mục gốc của User
            $users = User::factory()->count(3)->create()->each(function ($user) {
                MediaFolder::create([
                    'user_id' => $user->id,
                    'name' => 'Root Folder - ' . $user->name,
                    'path' => null,
                    'parent_id' => null,
                ]);
            });
        }

        $userId = User::get()->random()->id;

        //  https://motgame.vn/ngam-yae-miko-genshin-impact-phien-ban-cosplay-tran-vien-35215.html
        //  https://motgame.vn/sandu-69-dep-trong-veo-thuan-khiet-khi-cosplay-lincia-ben-bo-suoi-35189.html
        //  https://motgame.vn/ngam-kiana-dien-swimsuit-phien-ban-doi-thuc-cuc-muot-35203.html
        //  https://motgame.vn/cosplay-tamamo-nang-cao-xinh-dep-dua-nghich-ben-bo-bien-dem-35180.html
        //  https://motgame.vn/cosplay-long-mei-new-year-bo-hinh-tung-bung-don-nam-moi-35199.html
        //  https://motgame.vn/cosplay-giao-dien-moi-cua-ellen-jo-trong-zzz-cuc-ngau-35198.html

        $url = 'https://motgame.vn/ngam-yae-miko-genshin-impact-phien-ban-cosplay-tran-vien-35215.html';

        $xpath = MediaCrawlHelper::fetchDom($url);
        if (!$xpath) {
            $this->command->error("Không tải được HTML từ $url");
            return;
        }

        $this->command->info("✅ Bắt đầu cào ảnh từ $url");

        $folder = MediaCrawlHelper::crawlBreadcrumbToFolder($xpath, $userId);
        $tagIds = MediaCrawlHelper::crawlTags($xpath);
        MediaCrawlHelper::crawlImagesAndSave($xpath, $url, $userId, $folder, $tagIds);

        $this->command->info("✅ Cào xong ảnh từ $url");
    }
}
