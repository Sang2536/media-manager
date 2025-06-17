<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $table = 'media_files';

    protected $fillable = [
        'user_id', '
        filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'thumbnail_path',
        'folder_id',
        'is_public'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function folder() {
        return $this->belongsTo(MediaFolder::class);
    }

    public function tags() {
        return $this->belongsToMany(MediaTag::class, 'media_file_tag');
    }

    public function metadata() {
        return $this->hasMany(MediaMetadata::class);
    }
}
