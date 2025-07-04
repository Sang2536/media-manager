<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'media_folder_id'   => $this->name,
            'filename'          => $this->filename,
            'original_name'     => $this->original_name,
            'mime_type'         => $this->mime_type,
            'size'              => $this->size,
            'path'              => $this->path,
            'thumbnail_path'    => $this->thumbnail_path,
            'source_url'        => $this->source_url,
            'storage'           => $this->storage,
            'is_locked'         => $this->is_locked,
            'is_shared'         => $this->is_shared,
            'is_favorite'       => $this->is_favorite,
            'comments'          => $this->comments,
            'permissions'       => $this->permissions,
            'last_opened_at'    => $this->last_opened_at,
            'created_at'        => $this->created_at,
        ];
    }
}
