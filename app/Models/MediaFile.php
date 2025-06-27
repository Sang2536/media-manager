<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    protected $table = 'media_files';

    protected $fillable = [
        'user_id',
        'media_folder_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'thumbnail_path',
        'source_url',
        'storage',
        'is_locked',
        'is_shared',
        'is_favorite',
        'comments',
        'permissions',
        'last_opened_at'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_shared' => 'boolean',
        'is_favorite' => 'boolean',
        'permissions' => 'array',
        'last_opened_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function tags() {
        return $this->belongsToMany(MediaTag::class, 'media_file_tag', 'media_file_id', 'media_tag_id');
    }

    public function metadata() {
        return $this->hasMany(MediaMetadata::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->path)) {
            return null;
        }

        return Storage::disk('public')->url($this->path);
    }
}
