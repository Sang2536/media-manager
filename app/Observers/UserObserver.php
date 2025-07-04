<?php

namespace App\Observers;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\User;
use App\Services\MediaLogService;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    public function created(User $user): void
    {
        try {
            DB::transaction(fn () => $this->createRootFolderForUser($user));
        } catch (\Throwable $e) {
            MediaLogService::custom(
                action: 'create_error',
                targetType: 'User',
                targetId: $user->id,
                description: 'Lỗi khi tạo user hoặc thư mục gốc',
                data: ['error' => $e->getMessage()]
            );
        }
    }

    public function deleting(User $user): void
    {
        try {
            DB::transaction(fn () => $this->handleUserSoftDelete($user));
        } catch (\Throwable $e) {
            MediaLogService::custom(
                action: 'delete_error',
                targetType: 'User',
                targetId: $user->id,
                description: 'Lỗi khi xoá user hoặc dữ liệu liên quan',
                data: ['error' => $e->getMessage()]
            );
        }
    }

    // ========== CREATED ==========
    protected function createRootFolderForUser(User $user): MediaFolder
    {
        return MediaFolder::create([
            'user_id'   => $user->id,
            'parent_id' => null,
            'name'      => 'Root - ' . preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $user->name),
            'slug'      => null,
            'path'      => '',
            'depth'     => 0,
            'storage'   => 'local',
            'kind'      => 'folder',
            'is_locked' => true,
        ]);
    }

    // ========== DELETING ==========
    protected function handleUserSoftDelete(User $user): void
    {
        // Soft delete tất cả thư mục của user (đã bao gồm file con)
        $rootFolders = MediaFolder::where('user_id', $user->id)->get();

        foreach ($rootFolders as $folder) {
            $this->softDeleteFolderRecursive($folder);
        }

        // Soft delete các file không nằm trong folder
        MediaFile::where('user_id', $user->id)
            ->whereNull('folder_id')
            ->each(fn ($file) => $file->delete());
    }

    protected function softDeleteFolderRecursive(MediaFolder $folder): void
    {
        $folder->children->each(fn ($child) => $this->softDeleteFolderRecursive($child));
        $folder->files->each(fn ($file) => $file->delete());
        $folder->delete();
    }
}
