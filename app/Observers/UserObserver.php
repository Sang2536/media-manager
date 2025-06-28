<?php

namespace App\Observers;

use App\Models\User;
use App\Models\MediaFolder;

class UserObserver
{
    public function created(User $user): void
    {
        MediaFolder::create([
            'user_id'   => $user->id,
            'parent_id' => null,
            'name'      => 'Root - ' . $user->name,
            'slug'      => null,
            'path'      => '/',
            'depth'     => 0,
            'storage'   => 'local',
            'kind'      => 'folder',
            'is_locked' => true, // Root folder không xoá được
        ]);
    }
}
