<?php

namespace Tests\Feature;

use App\DataTransferObjects\MediaFileData;
use App\Helpers\MediaFileHelper;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaFileHelperTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MediaFolder $folder;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo user và observer sẽ tự tạo folder gốc
        $this->user = User::factory()->create();
        $this->folder = MediaFolder::where('user_id', $this->user->id)->firstOrFail();

        // Giả lập hệ thống lưu trữ file
        Storage::fake('public');
    }

    /** @test */
    public function it_can_store_uploaded_file()
    {
        $file = UploadedFile::fake()->image('example.jpg');

        $path = MediaFileHelper::storeUploadedFile($file, $file->name, $this->folder);

        Storage::disk('public')->assertExists($path);
    }

    /** @test */
    public function it_can_create_media_file_from_dto()
    {
        $file = UploadedFile::fake()->image('sample.jpg');
        $path = MediaFileHelper::storeUploadedFile($file, $file->name, $this->folder);

        $dto = new MediaFileData(
            userId: $this->user->id,
            file: $file,
            filename: $file->getClientOriginalName(),
            path: $path,
            mediaFolderId: $this->folder->id,
        );

        $mediaFile = MediaFileHelper::createMediaFileFromDto($dto);

        $this->assertDatabaseHas('media_files', [
            'id'       => $mediaFile->id,
            'filename' => basename($path),
        ]);
    }

    /** @test */
    public function it_can_update_media_file_info()
    {
        $mediaFile = MediaFile::create([
            'user_id'         => $this->user->id,
            'media_folder_id' => $this->folder->id,
            'filename'        => 'test.jpg',
            'original_name'   => 'test.jpg',
            'mime_type'       => 'image/jpeg',
            'size'            => 1000,
            'path'            => 'media/test/test.jpg',
            'storage'         => 'local',
        ]);

        $dto = new MediaFileData(
            userId: $mediaFile->user_id,
            file: UploadedFile::fake()->image('fake.jpg'),
            path: $mediaFile->path,
            mediaFolderId: $mediaFile->media_folder_id,
            comments: 'Updated comment',
        );

        MediaFileHelper::updateMediaFileInfo($mediaFile, $dto);

        $this->assertEquals('Updated comment', $mediaFile->fresh()->comments);
    }

    /** @test */
    public function it_can_attach_tags_to_media_file()
    {
        // Tạo media file
        $mediaFile = MediaFile::create([
            'user_id'         => $this->user->id,
            'media_folder_id' => $this->folder->id,
            'filename'        => 'test.jpg',
            'original_name'   => 'test.jpg',
            'mime_type'       => 'image/jpeg',
            'size'            => 1000,
            'path'            => 'media/test/test.jpg',
            'storage'         => 'local',
        ]);

        // Tạo các tag (dùng model để đảm bảo xử lý đầy đủ slug, observer)
        $tags = collect([
            ['name' => 'Tag 1'],
            ['name' => 'Tag 2'],
            ['name' => 'Tag 3'],
        ])->map(fn ($data) => \App\Models\MediaTag::create($data));

        $tagIds = $tags->pluck('id')->toArray();

        // Gắn tag
        \App\Helpers\MediaFileHelper::attachTags($mediaFile, $tagIds);

        // Reload quan hệ
        $mediaFile->refresh();

        // So sánh danh sách ID
        $this->assertEqualsCanonicalizing($tagIds, $mediaFile->tags->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_attach_metadata_to_media_file()
    {
        $mediaFile = MediaFile::create([
            'user_id'         => $this->user->id,
            'media_folder_id' => $this->folder->id,
            'filename'        => 'test.jpg',
            'original_name'   => 'test.jpg',
            'mime_type'       => 'image/jpeg',
            'size'            => 1000,
            'path'            => 'media/test/test.jpg',
            'storage'         => 'local',
        ]);

        $metadata = [
            ['key' => 'author', 'value' => 'John'],
            ['key' => 'license', 'value' => 'MIT'],
        ];

        MediaFileHelper::attachMetadata($mediaFile, $metadata);

        $this->assertDatabaseHas('media_metadata', [
            'media_file_id' => $mediaFile->id,
            'key'           => 'author',
            'value'         => 'John',
        ]);

        $this->assertDatabaseHas('media_metadata', [
            'media_file_id' => $mediaFile->id,
            'key'           => 'license',
            'value'         => 'MIT',
        ]);
    }
}
