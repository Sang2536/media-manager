<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTag extends Model
{
    protected $table = 'media_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = str()->slug($tag->name);
            }
        });
    }

    public function files()
    {
        return $this->belongsToMany(MediaFile::class, 'media_file_tag', 'media_tag_id', 'media_file_id')
            ->withTimestamps();
    }


    public function folders() {
        return $this->belongsToMany(MediaFolder::class, 'media_folder_tag', 'media_tag_id', 'media_folder_id')
            ->withTimestamps();
    }
}
