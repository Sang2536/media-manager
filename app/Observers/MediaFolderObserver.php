<?php

namespace App\Observers;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaLogService;

class MediaFolderObserver
{
    public function deleting(MediaFolder $folder): void
    {
        if (! $folder->isForceDeleting()) {
            $this->softDeleteFolder($folder);
        } else {
            $this->forceDeleteFolder($folder);
        }
    }

    protected function softDeleteFolder(MediaFolder $folder): void
    {
        $this->softDeleteFiles($folder);
        $this->softDeleteChildren($folder);
    }

    protected function softDeleteFiles(MediaFolder $folder): void
    {
        foreach ($folder->files as $file) {
            $file->delete(); // soft delete
        }
    }

    protected function softDeleteChildren(MediaFolder $folder): void
    {
        foreach ($folder->children as $child) {
            $child->delete(); // soft delete
        }
    }

    protected function forceDeleteFolder(MediaFolder $folder): void
    {
        $this->forceDeleteFiles($folder);
        $this->forceDeleteChildren($folder);
    }

    protected function forceDeleteFiles(MediaFolder $folder): void
    {
        foreach ($folder->files()->withTrashed()->get() as $file) {
            try {
                $this->deletePhysicalFile($file->storage, $file->path);
                $this->deletePhysicalFile($file->storage, $file->thumbnail_path);

                $file->metadata()->delete();
                $file->tags()->detach();
                $file->forceDelete();
            } catch (\Throwable $e) {
                $this->logFileDeletionError($file->id, $folder->id, $file->path, $e->getMessage());
            }
        }
    }

    protected function forceDeleteChildren(MediaFolder $folder): void
    {
        foreach ($folder->children()->withTrashed()->get() as $child) {
            $child->forceDelete();
        }
    }

    protected function deletePhysicalFile(string $disk, ?string $path): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    protected function logFileDeletionError(int $fileId, int $folderId, ?string $path, string $message): void
    {
        MediaLogService::custom(
            action: 'force_delete_error',
            targetType: 'MediaFile',
            targetId: $fileId,
            description: 'Lỗi khi xoá file trong folder',
            data: compact('fileId', 'folderId', 'path', 'message')
        );

        Log::error('Failed to delete file during folder force delete', compact('fileId', 'folderId', 'path', 'message'));
    }
}
