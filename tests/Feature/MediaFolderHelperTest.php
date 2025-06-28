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

    //  Test save folder from breadcrumb

    #[Test]
    public function test_can_create_nested_folders_from_breadcrumb()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        // Gọi tạo breadcrumb
        $folder = MediaFolderHelper::saveFromBreadcrumb('Parent/Child/Sub', $user->id);

        // Kiểm tra folder cuối cùng (depth)
        $this->assertInstanceOf(MediaFolder::class, $folder);
        $this->assertEquals('Sub', $folder->name);

        // Kiểm tra folder cha (depth - 1)
        $child = $folder->parent;
        $this->assertEquals('Child', $child->name);
        $this->assertEquals($user->id, $child->user_id);

        // Kiểm tra folder cha (depth - 2)
        $parent = $child->parent;
        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals($user->id, $parent->user_id);

        //  Kiểm tra folder root (depth - 3)
        $rootFolder = MediaFolderHelper::getRootFolder($user->id);
        $this->assertEquals('Root - ' . $user->name, $rootFolder->name);
        $this->assertEquals(0, $rootFolder->depth);

        // Tổng số folder tạo ra phải là 4 (Root Folder + Parent/Child/Sub)
        $this->assertEquals(4, MediaFolder::count()); // Root/Parent/Child/Sub
    }

    #[Test]
    public function test_duplicate_breadcrumb_should_throw_exception()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cây thư mục 'A/B' đã tồn tại.");

        $user = User::factory()->create();

        // Lần đầu tạo thành công
        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id);

        // Lần thứ hai sẽ lỗi
        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id);
    }

    #[Test]
    public function test_can_update_last_folder_in_breadcrumb()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $old = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);

        // Rename "C" thành "D"
        $updated = MediaFolderHelper::saveFromBreadcrumb('A/B/D', $user->id, null, $old);

        $this->assertEquals('D', $updated->name);
        $this->assertEquals($old->id, $updated->id);
        $this->assertEquals(4, MediaFolder::count()); // Root/A/B/C
    }

    #[Test]
    public function test_subfolder_can_be_created_under_custom_parent()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $base = MediaFolderHelper::saveFromBreadcrumb('X', $user->id);

        $sub = MediaFolderHelper::saveFromBreadcrumb('Y/Z', $user->id, $base->id);

        $this->assertEquals('Z', $sub->name);
        $this->assertEquals($base->id, $sub->parent->parent_id);
        $this->assertEquals(4, MediaFolder::count()); // Root -> X -> Y -> Z
    }
}
