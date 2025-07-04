<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'created_at'    => $this->created_at,
            'folders_count' => $this->folders_count ?? 0,
            'files_count'   => $this->files_count ?? 0,
        ];
    }
}
