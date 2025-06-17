<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $table = 'media_folders';

    protected $fillable = [
        'user_id',
        'name',
        'parent_id'
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
}
