<?php

namespace App\Observers;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;

class MediaFileObserver
{
    public function deleting(MediaFile $file): void
    {
        // Xóa file gốc
        if ($file->path && Storage::disk($file->storage)->exists($file->path)) {
            Storage::disk($file->storage)->delete($file->path);
        }

        // Xóa thumbnail nếu có
        if ($file->thumbnail_path && Storage::disk($file->storage)->exists($file->thumbnail_path)) {
            Storage::disk($file->storage)->delete($file->thumbnail_path);
        }

        // Xóa metadata (nếu không có cascade)
        $file->metadata()->delete();

        // Gỡ tag liên kết
        $file->tags()->detach();
    }
}
