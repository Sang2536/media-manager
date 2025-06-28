<?php

namespace App\DataTransferObjects;

use App\Models\MediaFolder;

class MediaFolderData
{
    public function __construct(
        public readonly int $userId,
        public readonly ?int $parentId,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $path,
        public readonly ?int $depth,
        public readonly ?string $storage,
        public readonly ?string $kind,
        public readonly ?string $folderType,
        public readonly ?bool $isLocked,
        public readonly ?bool $isShared,
        public readonly ?bool $isFavorite,
        public readonly ?string $thumbnail,
        public readonly ?string $comments,
        public readonly ?array $permissions,
        public readonly null|string|\DateTimeInterface $lastOpenedAt,

    ) {}

    public static function fromBasic(string $name, int $userId, ?int $parentId = null): self
    {
        return new self(
            userId: $userId,
            parentId: $parentId,
            name: $name,
            slug: null,
            path: null,
            depth: null,
            storage: 'local',
            kind: 'folder',
            folderType: null,
            isLocked: false,
            isShared: false,
            isFavorite: false,
            thumbnail: null,
            comments: null,
            permissions: null,
            lastOpenedAt: null,
        );
    }

    public static function fromExisting(MediaFolder $folder, string $newName, ?int $newParentId = null): self
    {
        return new self(
            userId: $folder->user_id,
            parentId: $newParentId ?? $folder->parent_id,
            name: $newName,
            slug: $folder->slug,
            path: $folder->path ?? str()->slug($newName),
            depth: $folder->depth,
            storage: $folder->storage,
            kind: $folder->kind,
            folderType: $folder->folder_type,
            isLocked: $folder->is_locked,
            isShared: $folder->is_shared,
            isFavorite: $folder->is_favorite,
            thumbnail: $folder->thumbnail,
            comments: $folder->comments,
            permissions: $folder->permissions,
            lastOpenedAt: $folder->last_opened_at,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'slug' => $this->slug,
            'path' => $this->path,
            'depth' => $this->depth,
            'storage' => $this->storage,
            'kind' => $this->kind,
            'folder_type' => $this->folderType,
            'is_locked' => $this->isLocked,
            'is_shared' => $this->isShared,
            'is_favorite' => $this->isFavorite,
            'thumbnail' => $this->thumbnail,
            'comments' => $this->comments,
            'permissions' => $this->permissions,
            'last_opened_at' => $this->lastOpenedAt,
        ];
    }
}
