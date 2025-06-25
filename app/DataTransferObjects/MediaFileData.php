<?php

namespace App\DataTransferObjects;

use Illuminate\Http\UploadedFile;

class MediaFileData
{
    public function __construct(
        public readonly int $userId,
        public readonly UploadedFile $file,
        public readonly string $path,
        public readonly int $mediaFolderId,
        public readonly bool $isPublic = true,
        public readonly ?string $thumbnailPath = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'filename' => basename($this->path),
            'original_name' => $this->file->getClientOriginalName(),
            'mime_type' => $this->file->getClientMimeType(),
            'size' => $this->file->getSize(),
            'path' => $this->path,
            'thumbnail_path' => $this->thumbnailPath,
            'media_folder_id' => $this->mediaFolderId,
            'is_public' => $this->isPublic,
        ];
    }
}
