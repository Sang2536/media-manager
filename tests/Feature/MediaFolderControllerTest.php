<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Helpers\MediaFolderHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaFolderControllerTest extends TestCase
{
    use RefreshDatabase;

    //  @test G1: Test tạo folder (thuoojc root folder)
    #[Test]
    public function test_user_can_create_folder_under_root()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Lấy thư mục gốc đã được tạo cùng user
        $rootFolder = MediaFolderHelper::getRootFolder($user->id);

        $response = $this->post(route('media-folders.store'), [
            'folder_name' => 'New Folder',
        ]);

        $response->assertRedirect(route('media-folders.index'));

        // ✅ Kiểm tra root folder tồn tại
        $this->assertDatabaseHas('media_folders', [
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name),
        ]);

        // ✅ Kiểm tra folder mới được tạo nằm trong thư mục gốc
        $this->assertDatabaseHas('media_folders', [
            'user_id' => $user->id,
            'name' => 'New Folder',
            'parent_id' => $rootFolder->id,
        ]);
    }

    //  @test G2: Test sửa folder
    #[Test]
    public function test_user_can_update_folder()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $rootFolder = MediaFolderHelper::getRootFolder($user->id);

        $folder = MediaFolder::create([
            'user_id' => $user->id,
            'parent_id' => $rootFolder->id,
            'name' => 'Old Name',
            'path' => $rootFolder->path . '/Old Name',
            'storage' => 'local',
            'kind' => 'folder',
        ]);

        $response = $this->put(route('media-folders.update', $folder), [
            'folder_name' => 'Renamed Folder',
        ]);

        $response->assertRedirect(route('media-folders.index'));

        $this->assertDatabaseHas('media_folders', [
            'id' => $folder->id,
            'name' => 'Renamed Folder',
        ]);
    }

    //  @test G3: Test không thể sửa folder của người khác
    #[Test]
    public function test_user_cannot_update_folder_of_another_user()
    {
        $userA = User::factory()->create(); // chủ folder
        $userB = User::factory()->create(); // người test

        $rootFolderA = MediaFolderHelper::getRootFolder($userA->id);

        $folder = MediaFolder::create([
            'user_id' => $userA->id,
            'parent_id' => $rootFolderA->id,
            'name' => 'Deletable Folder',
            'path' => $rootFolderA->path . '/Deletable Folder',
            'storage' => 'local',
            'kind' => 'folder',
        ]);

        $this->actingAs($userB); // Login người B

        $response = $this->patchJson(route('media-folders.update', $folder), [
            'folder_name' => 'Hacked',
        ]);

        $response->assertStatus(403); // Vì userB không có quyền
        $this->assertDatabaseHas('media_folders', ['id' => $folder->id]);
    }

    //  @test G4: Test xóa folder hợp lệ
    #[Test]
    public function test_user_can_delete_own_folder()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $rootFolder = MediaFolderHelper::getRootFolder($user->id);

        $folder = MediaFolder::create([
            'user_id' => $user->id,
            'parent_id' => $rootFolder->id,
            'name' => 'Deletable Folder',
            'path' => $rootFolder->path . '/Deletable Folder',
            'storage' => 'local',
            'kind' => 'folder',
        ]);

        $response = $this->delete(route('media-folders.destroy', $folder));

        $response->assertRedirect(route('media-folders.index'));

        $this->assertDatabaseMissing('media_folders', [
            'id' => $folder->id,
        ]);
    }

    //  @test G5: Test không thể xóa root folder
    #[Test]
    public function test_user_cannot_delete_root_folder()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rootFolder = MediaFolderHelper::getRootFolder($user->id);
        $response = $this->deleteJson(route('media-folders.destroy', $rootFolder));

        $response->assertStatus(400);
        $this->assertDatabaseHas('media_folders', ['id' => $rootFolder->id]);
    }

    //  @test G6: Test không thể xóa folder của người khác
    #[Test]
    public function test_user_cannot_delete_folder_of_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $rootFolder2 = MediaFolderHelper::getRootFolder($user2->id);

        $folder = MediaFolder::create([
            'user_id' => $user2->id,
            'parent_id' => $rootFolder2->id,
            'name' => 'User2 Folder',
            'path' => $rootFolder2->path . '/User2 Folder',
            'storage' => 'local',
            'kind' => 'folder',
        ]);

        $this->actingAs($user1);
        $response = $this->deleteJson(route('media-folders.destroy', $folder));

        $response->assertStatus(403);
        $this->assertDatabaseHas('media_folders', ['id' => $folder->id]);
    }

    //  @test G7: Test không thể xóa folder chứa file
    #[Test]
    public function test_user_cannot_delete_folder_with_files()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rootFolder = MediaFolderHelper::getRootFolder($user->id);

        $folder = MediaFolder::create([
            'user_id' => $user->id,
            'parent_id' => $rootFolder->id,
            'name' => 'With Files',
            'path' => $rootFolder->path . '/With Files',
            'storage' => 'local',
            'kind' => 'folder',
        ]);

        MediaFile::create([
            'user_id' => $user->id,
            'media_folder_id' => $folder->id,
            'filename' => 'file.jpg',
            'original_name' => 'file.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'path' => 'media/file.jpg',
            'storage' => 'local',
        ]);

        $response = $this->deleteJson(route('media-folders.destroy', $folder));

        $response->assertStatus(400);
        $this->assertDatabaseHas('media_folders', ['id' => $folder->id]);
    }
}
