<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index() {
        $mediaFiles = MediaFile::with(['user', 'folder', 'tags', 'metadata'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('media.index', compact('mediaFiles'));
    }
}
