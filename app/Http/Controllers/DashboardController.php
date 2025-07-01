<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaMetadata;
use App\Models\MediaTag;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Media Folder Details
        $totalFolders = MediaFolder::count();

        //  Media File Details
        $mediaFiles = new MediaFile();
        $totalFiles = $mediaFiles->count();

        $mediaTypes = MediaFile::selectRaw("SUBSTRING_INDEX(mime_type, '/', 1) as category, COUNT(*) as total")
            ->groupBy('category')
            ->pluck('total', 'category');

        $mediaFileDetails = [
            'totalSize' => $mediaFiles->sum('size'),
            'imageCount' => $mediaTypes['image'] ?? 0,
            'videoCount' => $mediaTypes['video'] ?? 0,
            'mostCommonType' => $mediaFiles->select('mime_type')->groupBy('mime_type')->orderByRaw('COUNT(*) DESC')->value('mime_type'),
            'recentFiles' => $mediaFiles->latest()->take(5)->get(),
        ];

        //  Media Tag Details
        $totalTags = MediaTag::count();
        $popularTags = MediaTag::withCount('files')->orderByDesc('files_count')->take(5)->get();

        //  Media Metadata Details
        $recentMetadata = MediaMetadata::latest()->take(5)->get();

        return view('media.menu.dashboard', compact(
            'totalFolders',
            'totalFiles',
            'mediaFileDetails',
            'totalTags',
            'popularTags',
            'recentMetadata',
        ));
    }

    public function guide()
    {
        return view('media.guide.index');
    }
}
