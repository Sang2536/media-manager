<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTag extends Model
{
    protected $table = 'media_tags';

    protected $fillable = [
        'name'
    ];

    public function files() {
        return $this->belongsToMany(MediaFile::class, 'media_file_tag');
    }
}
