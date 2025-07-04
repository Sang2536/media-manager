<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaFolderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'path'           => $this->path,
            'depth'          => $this->depth,
            'storage'        => $this->storage,
            'kind'           => $this->kind,
            'folder_type'    => $this->folder_type,
            'is_locked'      => $this->is_locked,
            'is_shared'      => $this->is_shared,
            'is_favorite'    => $this->is_favorite,
            'thumbnail'      => $this->thumbnail,
            'comments'       => $this->comments,
            'permissions'    => $this->permissions,
            'last_opened_at' => $this->last_opened_at,
            'created_at'        => $this->created_at,
            'files_count'    => $this->files_count ?? 0,
            'parent'         => $this->whenLoaded('parent'),
            'children'       => MediaFolderResource::collection($this->whenLoaded('children')),
        ];
    }
}
