<?php

namespace Tests\Feature;

use App\Helpers\MediaFolderHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaFolderHelperTest extends TestCase
{
    use RefreshDatabase;

    // @test G1: Tạo cây thư mục lồng nhau từ breadcrumb
    #[Test]
    public function test_creates_nested_folders_from_breadcrumb()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $folder = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);

        $this->assertEquals('C', $folder->name);
        $this->assertEquals(3, count(MediaFolderHelper::buildBreadcrumb($folder)) - 1);
    }

    // @test G2: Không cho tạo trùng cây thư mục
    #[Test]
    public function test_throws_exception_when_creating_duplicate_breadcrumb()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cây thư mục 'A/B' đã tồn tại.");

        $user = User::factory()->create();
        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id);
        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id);
    }

    // @test G3: Rename folder cuối cùng trong breadcrumb
    #[Test]
    public function test_can_rename_last_folder_in_breadcrumb()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $old = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);

        $renamed = MediaFolderHelper::saveFromBreadcrumb('A/B/D', $user->id, null, $old, 'rename');

        $this->assertEquals('D', $renamed->name);
        $this->assertEquals($old->id, $renamed->id);
        $this->assertEquals(
            ['Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name), 'A', 'B', 'D'],
            array_map(fn($f) => $f->name, MediaFolderHelper::buildBreadcrumb($renamed))
        );
    }

    // @test G4: Move folder vào parent khác
    #[Test]
    public function test_can_move_folder_to_another_parent()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $folder = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);
        $newParent = MediaFolderHelper::saveFromBreadcrumb('X/Y', $user->id);

        $moved = MediaFolderHelper::saveFromBreadcrumb('X/Y/C', $user->id, null, $folder, 'move');

        $this->assertEquals($folder->id, $moved->id);
        $this->assertEquals('C', $moved->name);
        $this->assertEquals(
            ['Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name), 'X', 'Y', 'C'],
            array_map(fn($f) => $f->name, MediaFolderHelper::buildBreadcrumb($moved))
        );
    }

    // @test G5: Rename và move cùng lúc
    #[Test]
    public function test_can_rename_and_move_folder()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $folder = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);
        $newParent = MediaFolderHelper::saveFromBreadcrumb('X/Y', $user->id);

        $updated = MediaFolderHelper::saveFromBreadcrumb('X/Y/D', $user->id, null, $folder, 'rename_move');

        $this->assertEquals('D', $updated->name);
        $this->assertEquals($folder->id, $updated->id);
        $this->assertEquals(
            ['Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name), 'X', 'Y', 'D'],
            array_map(fn($f) => $f->name, MediaFolderHelper::buildBreadcrumb($updated))
        );
    }

    // @test G6: Không cho move folder vào chính nó hoặc con của nó
    #[Test]
    public function test_prevents_moving_folder_into_its_descendant()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Không thể di chuyển thư mục vào chính nó hoặc thư mục con của nó.');

        $user = User::factory()->create();
        $folder = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);
        $sub = MediaFolderHelper::saveFromBreadcrumb('A/B/C/Sub', $user->id);

        MediaFolderHelper::saveFromBreadcrumb('A/B/C/Sub/C', $user->id, null, $folder, 'move');
    }

    // @test G7: Không cho rename trùng tên trong cùng parent
    #[Test]
    public function test_prevents_rename_to_existing_sibling_name()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Thư mục 'B' đã tồn tại trong cùng cấp.");

        $user = User::factory()->create();
        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id);
        $folder = MediaFolderHelper::saveFromBreadcrumb('A/C', $user->id);

        MediaFolderHelper::saveFromBreadcrumb('A/B', $user->id, null, $folder, 'rename');
    }

    // @test G8: Tạo thư mục dưới thư mục cha cụ thể
    #[Test]
    public function test_can_create_under_specific_parent()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $base = MediaFolderHelper::saveFromBreadcrumb('Base', $user->id);

        $sub = MediaFolderHelper::saveFromBreadcrumb('Sub1/Sub2', $user->id, $base->id);

        $this->assertEquals('Sub2', $sub->name);
        $this->assertEquals(
            ['Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name), 'Base', 'Sub1', 'Sub2'],
            array_map(fn($f) => $f->name, MediaFolderHelper::buildBreadcrumb($sub))
        );
    }

    // @test G9: Tự động tạo root folder khi tạo user
    #[Test]
    public function it_creates_root_folder_when_user_created()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create(['name' => 'Test User']);

        $root = MediaFolderHelper::getRootFolder($user->id);

        $this->assertNotNull($root);
        $this->assertEquals("Root - Test User", $root->name);
        $this->assertTrue($root->is_locked);
        $this->assertEquals(0, $root->depth);
    }

    // @test G10: Kiểm tra breadcrumb dạng chuỗi
    #[Test]
    public function test_can_build_breadcrumb_string()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $folder = MediaFolderHelper::saveFromBreadcrumb('A/B/C', $user->id);

        $breadcrumb = MediaFolderHelper::buildBreadcrumb($folder, true);
        $this->assertEquals('Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name) . '/A/B/C', $breadcrumb);
    }
}
