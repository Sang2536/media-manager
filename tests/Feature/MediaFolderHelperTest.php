<?php

namespace Tests\Feature;

use App\Helpers\MediaFolderHelper;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaFolderHelperTest extends TestCase
{
    use RefreshDatabase;

    public function create_an_existing_simulation_folder(): array
    {
        // Tạo user và folder gốc
        $user = User::factory()->create();
        $this->actingAs($user);

        $root = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'Root - ' . $user->name,
            'path' => str()->slug('Root - ' . $user->name),
        ]);

        // Giả lập cấu trúc sẵn có: Admin/Cosplay/HSR
        $admin = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'Admin',
            'parent_id' => $root->id,
            'path' => $root->path . '/' . str()->slug('Admin'),
        ]);

        $cosplay = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'Cosplay',
            'parent_id' => $admin->id,
            'path' => $admin->path . '/' . str()->slug('Cosplay'),
        ]);

        $hsr = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'HSR',
            'parent_id' => $cosplay->id,
            'path' => $admin->path . '/' . str()->slug('HSR'),
        ]);

        return [
            $user,
            $root,
            $admin,
            $cosplay,
            $hsr,
        ];
    }

    #[Test]
    public function test_save_from_breadcrumb_creates_missing_only(): void
    {
        //  Tạo một thư mục mô phỏng hiện có
        [$user, $root, $admin, $cosplay, $hsr] = $this->create_an_existing_simulation_folder();

        // Gọi hàm với breadcrumb có thêm thư mục mới: Firefly
        $folder = MediaFolderHelper::saveFromBreadcrumb('Admin/Cosplay/HSR/Firefly', $user->id, $root->id);

        //  kiểm tra thư mục Firefly là thư mục duy nhất mới được tạo
        $created = MediaFolder::where('user_id', $user->id)
            ->where('name', 'Firefly')
            ->where('parent_id', $hsr->id)
            ->first();

        $this->assertNotNull($created);

        // Kiểm tra folder được tạo
        $this->assertDatabaseHas('media_folders', [
            'name' => 'Firefly',
            'user_id' => $user->id,
            'parent_id' => $hsr->id,
        ]);

        // Kiểm tra hàm trả về đúng folder
        $this->assertEquals('Firefly', $folder->name);

        // Kiểm tra không tạo lại các thư mục cũ
        $this->assertEquals(5, MediaFolder::where('user_id', $user->id)->count());
    }

    #[Test]
    public function test_updates_path_for_all_children_when_folder_is_moved(): void
    {
        //  Tạo một thư mục mô phỏng hiện có
        [$user, $root, $admin, $cosplay, $hsr] = $this->create_an_existing_simulation_folder();

        // Tạo một folder khác để move Cosplay vào
        $newRoot = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'NewRoot' . $user->name,
            'path' => str()->slug('NewRoot - ' . $user->name),
        ]);

        // Di chuyển Cosplay → NewRoot
        $cosplay->parent_id = $newRoot->id;
        $cosplay->save();

        // Gọi hàm cập nhật path đệ quy
        MediaFolderHelper::rebuildPathRecursive($cosplay);

        // Làm mới lại các model từ DB
        $cosplay->refresh();
        $hsr->refresh();

        $this->assertEquals($newRoot->path . '/cosplay', $cosplay->path);
        $this->assertEquals($newRoot->path . '/cosplay/hsr', $hsr->fresh()->path);
    }

    #[Test]
    public function test_move_folder_and_update_paths_correctly(): void
    {
        //  Tạo một thư mục mô phỏng hiện có
        [$user, $root, $admin, $cosplay, $hsr] = $this->create_an_existing_simulation_folder();

        //  root/admin/cosplay/hsr  ->   root/admin/game/cosplay-hoyoverse/hsr

        // Tạo folder Game (mục tiêu mới)  ->  root/admin/game
        $game = MediaFolder::create([
            'user_id' => $user->id,
            'name' => 'Game',
            'path' => $admin->path . '/' . str()->slug('Game'),
            'parent_id' => $admin->id,
        ]);

        // Thực hiện move folder Cosplay → folder Game và đổi tên thành Cosplay HoYoverse
        $newName = 'Cosplay HoYoverse';
        $moved = MediaFolderHelper::moveFolderAndUpdatePaths($cosplay->id, $game->id, $newName);

        // Kiểm tra lại thông tin của folder Cosplay HoYoverse vừa move
        $this->assertEquals($newName, $moved->name);
        $this->assertEquals($game->path . '/' . str()->slug($newName), $moved->path);
        $this->assertEquals($game->id, $moved->parent_id);

        // Kiểm tra lại path, parent_id của HSR đã được cập nhật
        $hsr->refresh();
        $this->assertEquals($moved->path . '/' . str()->slug($hsr->name), $hsr->path);
        $this->assertEquals($moved->id, $hsr->parent_id);
    }
}
