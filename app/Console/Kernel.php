<?php

namespace App\Console;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;

class Kernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $expired = now()->subDays(30);

            // Xoá file trước để tránh ràng buộc DB
            MediaFile::onlyTrashed()
                ->where('deleted_at', '<', $expired)
                ->get()
                ->each(function ($file) {
                    if ($file->path && Storage::disk($file->storage)->exists($file->path)) {
                        Storage::disk($file->storage)->delete($file->path);
                    }

                    if ($file->thumbnail_path && Storage::disk($file->storage)->exists($file->thumbnail_path)) {
                        Storage::disk($file->storage)->delete($file->thumbnail_path);
                    }

                    $file->forceDelete();
                });

            // Sau đó mới xoá folder
            MediaFolder::onlyTrashed()
                ->where('deleted_at', '<', $expired)
                ->get()
                ->each(fn($folder) => $folder->forceDelete());
        })->daily();
    }
}
