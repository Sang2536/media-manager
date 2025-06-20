<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaMetadata extends Model
{
    protected $table = 'media_metadata';
    protected $fillable = [
        'media_file_id',
        'key',
        'value'
    ];

    public function file() {
        return $this->belongsTo(MediaFile::class, 'media_file_id');
    }
}
