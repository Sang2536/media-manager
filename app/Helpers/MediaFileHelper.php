<?php

namespace App\Helpers;

use App\DataTransferObjects\MediaFileData;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;

class MediaFileHelper
{
    public static function handleUploadFromDto(
        MediaFileData $dto,
        ?MediaFile $existingFile = null,
    ): MediaFile {
        if ($dto->file) {
            $path = self::storeUploadedFile($dto->file, $dto->filename, self::getTargetFolder($dto->mediaFolderId));
            $dto = $dto->withPath($path);
        }

        $mediaFile = $existingFile
            ? self::updateMediaFileInfo($existingFile, $dto)
            : self::createMediaFileFromDto($dto);

        self::attachTags($mediaFile, $dto->tags);
        self::attachMetadata($mediaFile, $dto->metadata);

        return $mediaFile;
    }

    public static function getTargetFolder(?int $folderId): ?MediaFolder
    {
        return $folderId ? MediaFolder::findOrFail($folderId) : null;
    }

    public static function storeUploadedFile(
        $file,
        ?string $filename = null,
        ?MediaFolder $folder,
        ?string $disk = 'public'
    ): string {
        $safeFolderName = $folder ? str()->slug($folder->name) : 'uncategorized';
        $folderPath = 'media/' . $safeFolderName;

        // Nếu có filename thì xử lý slug + đuôi mở rộng
        if ($filename) {
            $safeName = str()->slug(pathinfo($filename, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            return $file->storeAs($folderPath, $safeName . '.' . $extension, $disk);
        }

        // Nếu không có, tạo tên file duy nhất
        return $file->store($folderPath, $disk);
    }

    public static function createMediaFileFromDto(MediaFileData $dto): MediaFile
    {
        return MediaFile::create($dto->toArray());
    }

    public static function updateMediaFileInfo(MediaFile $file, MediaFileData $dto): MediaFile
    {
        $file->update($dto->toUpdateArray());
        return $file;
    }

    public static function attachTags(MediaFile $mediaFile, array $tags): void
    {
        if (empty($tags)) {
            $mediaFile->tags()->sync([]); // Xóa hết nếu không có tag
            return;
        }

        $tagIds = [];

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                // Trường hợp là ID
                $tagIds[] = (int) $tag;
            } elseif (is_string($tag)) {
                // Trường hợp là tên tag mới → tạo nếu chưa có
                $tagModel = MediaTag::firstOrCreate(
                    ['name' => $tag],
                    ['slug' => str()->slug($tag), 'color' => null]
                );

                $tagIds[] = $tagModel->id;
            }
        }

        $mediaFile->tags()->sync($tagIds);
    }

    public static function attachMetadata(MediaFile $mediaFile, array $metadata, bool $replace = true): void
    {
        if (empty($metadata)) {
            if ($replace) {
                $mediaFile->metadata()->delete(); // Xóa hết nếu không có metadata mới và cho phép replace
            }
            return;
        }

        $existing = $mediaFile->metadata->keyBy('key');
        $incomingKeys = [];

        foreach ($metadata as $item) {
            $key = trim($item['key'] ?? '');
            $value = $item['value'] ?? null;

            if (!$key) {
                continue; // Bỏ qua nếu key rỗng
            }

            $incomingKeys[] = $key;

            if ($existing->has($key)) {
                $existingItem = $existing->get($key);

                // Cập nhật nếu value khác nhau
                if ($existingItem->value !== $value) {
                    $existingItem->update(['value' => $value]);
                }
            } else {
                // Tạo mới metadata nếu chưa tồn tại
                $mediaFile->metadata()->create([
                    'key'   => $key,
                    'value' => $value,
                ]);
            }
        }

        // Xoá những metadata cũ không còn trong danh sách
        if ($replace) {
            $mediaFile->metadata()
                ->whereNotIn('key', $incomingKeys)
                ->delete();
        }
    }
}
