<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaFileHelper
{
    public static function generateSluggedFilename($url)
    {
        $filename = basename(parse_url($url, PHP_URL_PATH));
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $slug = Str::slug($name);
        return $slug . '.' . strtolower($ext);
    }

    public static function downloadImageToStorage($url, $folderSlug)
    {
        $filename = self::generateSluggedFilename($url);
        $folderPath = "media/{$folderSlug}";
        $fullPath = "{$folderPath}/{$filename}";

        $imageContents = file_get_contents($url);
        Storage::disk('public')->put($fullPath, $imageContents);

        return [
            'path' => $fullPath,
            'filename' => $filename,
            'original_name' => basename(parse_url($url, PHP_URL_PATH)),
            'mime_type' => self::guessMimeType($filename),
            'size' => strlen($imageContents),
        ];
    }

    public static function guessMimeType($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}
