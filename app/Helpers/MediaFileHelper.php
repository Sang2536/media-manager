<?php

namespace App\Helpers;

use App\DataTransferObjects\MediaFileData;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Normalizer;

class MediaFileHelper
{
    public static function normalizeFilename(string $originalName): string
    {
        $pathInfo = pathinfo($originalName, PATHINFO_FILENAME);
        $normalized = Normalizer::normalize($pathInfo, Normalizer::FORM_KD);
        return Str::slug($normalized);
    }

    public static function storeUploadedFile(UploadedFile $file, MediaFolder $folder): string
    {
        $normalizedFilename = self::normalizeFilename($file->getClientOriginalName());
        $filename = $normalizedFilename . '.' . $file->getClientOriginalExtension();
        $folderPath = 'media/' . $folder->name;

        return $file->storeAs($folderPath, $filename, 'public');
    }

    public static function createMediaFileFromDto(MediaFileData $dto): MediaFile
    {
        return MediaFile::create($dto->toArray());
    }

    public static function attachRandomTags(MediaFile $mediaFile, int $min = 1, int $max = 3): void
    {
        $tags = MediaTag::get();
        if ($tags->count()) {
            $mediaFile->tags()->attach($tags->random(rand($min, min($max, $tags->count())))->pluck('id'));
        }
    }

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
