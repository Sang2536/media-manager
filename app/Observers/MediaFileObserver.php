<?php

namespace App\Observers;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaLogService;

class MediaFileObserver
{
    public function deleting(MediaFile $file): void
    {
        if (! $file->isForceDeleting()) {
            $this->handleSoftDelete($file);
        } else {
            $this->handleForceDelete($file);
        }
    }

    protected function handleSoftDelete(MediaFile $file): void
    {
        $file->tags()->detach();
        $file->metadata()->delete();
    }

    protected function handleForceDelete(MediaFile $file): void
    {
        try {
            $this->deleteFileFromStorage($file->storage, $file->path);
            $this->deleteFileFromStorage($file->storage, $file->thumbnail_path);

            $file->metadata()->delete();
            $file->tags()->detach();
        } catch (\Throwable $e) {
            $this->logForceDeleteError($file, $e->getMessage());
        }
    }

    protected function deleteFileFromStorage(string $disk, ?string $path): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    protected function logForceDeleteError(MediaFile $file, string $message): void
    {
        MediaLogService::custom(
            action: 'force_delete_error',
            targetType: 'MediaFile',
            targetId: $file->id,
            description: 'Lỗi khi xoá vĩnh viễn MediaFile và các liên kết',
            data: [
                'file_id'        => $file->id,
                'file_path'      => $file->path,
                'thumbnail_path' => $file->thumbnail_path,
                'exception'      => $message,
            ]
        );

        Log::error('MediaFile force delete failed', [
            'file_id' => $file->id,
            'file_path' => $file->path,
            'error' => $message,
        ]);
    }
}
