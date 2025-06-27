<?php

namespace Feature;

use App\Helpers\MediaFileHelper;
use App\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaFileControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing a valid media file.
     */
    public function test_store_valid_media_file()
    {
        // Fake the storage
        Storage::fake('public');

        // Mock authentication
        $user = $this->createUser();
        Auth::login($user);

        // Create a folder
        $folder = MediaFolder::create();

        // Prepare request data
        $file = UploadedFile::fake()->image('test.jpg');
        $data = [
            'file' => $file,
            'folder_id' => $folder->id,
        ];

        $this->post(route('media-files.store'), $data);

        // Assertions
        Storage::disk('public')->assertExists("{$folder->id}/test.jpg");
        $this->assertDatabaseHas('media_files', [
            'media_folder_id' => $folder->id,
        ]);
    }

    /**
     * Test storing a media file with invalid data.
     */
    public function test_store_invalid_media_file()
    {
        // Mock authentication
        $user = $this->createUser();
        Auth::login($user);

        // Prepare request data with a missing `file`
        $data = [
            'folder_id' => 1,
        ];

        $response = $this->post(route('media-files.store'), $data);

        // Assertions
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test storing a media file with a non-existent folder ID.
     */
    public function test_store_file_with_nonexistent_folder()
    {
        // Fake the storage
        Storage::fake('public');

        // Mock authentication
        $user = $this->createUser();
        Auth::login($user);

        // Prepare request data
        $file = UploadedFile::fake()->image('example.jpg');
        $data = [
            'file' => $file,
            'folder_id' => 999, // Non-existent folder ID
        ];

        $response = $this->post(route('media-files.store'), $data);

        // Assertions
        $response->assertStatus(404);
    }

    /**
     * Test unauthorized user trying to store a file.
     */
    public function test_unauthorized_store_media_file()
    {
        // Fake the storage
        Storage::fake('public');

        // Prepare request data
        $file = UploadedFile::fake()->image('file.jpg');
        $data = [
            'file' => $file,
            'folder_id' => 1,
        ];

        $response = $this->post(route('media-files.store'), $data);

        // Assertions
        $response->assertRedirect(route('login'));
    }

    /**
     * Test storing a media file and attaching random tags.
     */
    public function test_store_file_with_attached_tags()
    {
        // Fake the storage
        Storage::fake('public');

        // Mock MediaFileHelper to attach tags
        $this->partialMock(MediaFileHelper::class, function ($mock) {
            $mock->shouldReceive('attachRandomTags')->once();
        });

        // Mock authentication
        $user = $this->createUser();
        Auth::login($user);

        // Create Folder
        $folder = MediaFolder::create();

        // Prepare request data
        $file = UploadedFile::fake()->image('image.jpg');
        $data = [
            'file' => $file,
            'folder_id' => $folder->id,
        ];

        $this->post(route('media-files.store'), $data);

        // Assertions
        Storage::disk('public')->assertExists("{$folder->id}/image.jpg");
        $this->assertDatabaseHas('media_files', ['media_folder_id' => $folder->id]);
    }

    /**
     * Helper function to create a user.
     */
    protected function createUser()
    {
        return \App\Models\User::create();
    }
}
