<?php

namespace App\DataTransferObjects;

use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;

class MediaFileData
{
    public function __construct(
        public readonly int $userId,
        public readonly ?UploadedFile $file,
        public readonly string $path,
        public readonly ?int $mediaFolderId = null,
        public readonly ?string $filename = null,
        public readonly ?string $originalName = null,
        public readonly ?string $thumbnailPath = null,
        public readonly ?string $sourceUrl = null,
        public readonly string $storage = 'local',
        public readonly bool $isLocked = false,
        public readonly bool $isShared = false,
        public readonly bool $isFavorite = false,
        public readonly ?string $comments = null,
        public readonly ?array $permissions = null,
        public readonly ?string $lastOpenedAt = null,
        public readonly ?array $tags = [],
        public readonly ?array $metadata = [],
    ) {}

    public function toArray(): array
    {
        return [
            'user_id'         => $this->userId,
            'filename'        => $this->filename ?? $this->file?->getClientOriginalName(),
            'original_name'   => $this->file?->getClientOriginalName(),
            'mime_type'       => $this->file?->getClientMimeType(),
            'size'            => $this->file?->getSize(),
            'path'            => $this->path,
            'thumbnail_path'  => $this->thumbnailPath,
            'media_folder_id' => $this->mediaFolderId,
            'source_url'      => $this->sourceUrl,
            'storage'         => $this->storage,
            'is_locked'       => $this->isLocked,
            'is_shared'       => $this->isShared,
            'is_favorite'     => $this->isFavorite,
            'comments'        => $this->comments,
            'permissions'     => $this->permissions,
            'last_opened_at'  => $this->lastOpenedAt,
        ];
    }

    public function toUpdateArray(): array
    {
        $data = [
            'filename'        => $this->filename,
            'media_folder_id' => $this->mediaFolderId,
            'comments'        => $this->comments,
            'is_locked'       => $this->isLocked,
            'is_shared'       => $this->isShared,
            'is_favorite'     => $this->isFavorite,
            'permissions'     => $this->permissions,
            'path'            => $this->path,
        ];

        // Nếu có file upload mới thì cập nhật các thông tin liên quan
        if ($this->file) {
            $data = array_merge($data, [
                'original_name' => $this->file->getClientOriginalName(),
                'mime_type'     => $this->file->getClientMimeType(),
                'size'          => $this->file->getSize(),
            ]);
        }

        return $data;
    }

    public static function fromRequest(MediaFileRequest $request, ?MediaFile $existingFile = null): self
    {
        $uploadedFile = $request->file('file');

        return new self(
            userId: $existingFile?->user_id ?? auth()->id() ?? 1,
            file: $uploadedFile,
            path: $existingFile?->path ?? '',
            filename: $request->input('filename') ?? $existingFile?->filename,
            originalName: $uploadedFile
                ? $uploadedFile->getClientOriginalName()
                : $existingFile?->original_name,
            thumbnailPath: $existingFile?->thumbnail_path,
            sourceUrl: $existingFile?->source_url,
            storage: $existingFile?->storage ?? 'local',
            isLocked: $request->boolean('is_locked', $existingFile?->is_locked ?? false),
            isShared: $request->boolean('is_shared', $existingFile?->is_shared ?? false),
            isFavorite: $request->boolean('is_favorite', $existingFile?->is_favorite ?? false),
            comments: $request->input('comments', $existingFile?->comments),
            permissions: $request->input('permissions', $existingFile?->permissions),
            lastOpenedAt: $existingFile?->last_opened_at?->toDateTimeString(),

            mediaFolderId: $request->input('folder_id'),
            tags: $request->input('tags', []),
            metadata: $request->input('metadata', []),
        );
    }
    public function withPath(string $path): self
    {
        return new self(
            userId: $this->userId,
            file: $this->file,
            path: $path,
            mediaFolderId: $this->mediaFolderId,
            filename: $this->filename,
            originalName: $this->originalName,
            thumbnailPath: $this->thumbnailPath,
            sourceUrl: $this->sourceUrl,
            storage: $this->storage,
            isLocked: $this->isLocked,
            isShared: $this->isShared,
            isFavorite: $this->isFavorite,
            comments: $this->comments,
            permissions: $this->permissions,
            lastOpenedAt: $this->lastOpenedAt,

            tags: $this->tags,
            metadata: $this->metadata,
        );
    }
}
