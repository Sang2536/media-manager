<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $table = 'media_folders';

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'slug',
        'path',
        'depth',
        'storage',
        'kind',
        'folder_type',
        'is_locked',
        'is_shared',
        'is_favorite',
        'thumbnail',
        'comments',
        'permissions',
        'last_opened_at',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_shared' => 'boolean',
        'is_favorite' => 'boolean',
        'permissions' => 'array',
        'last_opened_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function parent() {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function files() {
        return $this->hasMany(MediaFile::class);
    }

    public function tags() {
        return $this->belongsToMany(MediaTag::class, 'media_folder_tag', 'media_folder_id', 'media_tag_id');
    }
}
