<?php

namespace App\Models;

use App\Traits\LogsModelEvents;
use Illuminate\Database\Eloquent\Model;

class MediaMetadata extends Model
{
    use LogsModelEvents;

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
