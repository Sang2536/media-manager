<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\MediaFileData;
use App\Helpers\MediaFileHelper;
use App\Helpers\MediaFolderHelper;
use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'list');

        $userId = auth()->user()->id ?? 1;

        $htmlFolderSelect = MediaFolderHelper::renderFolderOptions($userId);

        $mediaFiles = MediaFile::with(['user', 'folder', 'tags', 'metadata'])
            ->orderByDesc('created_at')
            ->latest()
            ->paginate(12);

        return view('media.files.index', ['mediaFiles' => $mediaFiles, 'view' => $view, 'htmlFolderSelect' => $htmlFolderSelect]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->user()->id ?? null;

        $folders = MediaFolder::where('parent_id', null)->get();

        $tags = MediaTag::all();

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, null, 'media_file');

        return view('media.files.create', compact('folders', 'renderFolderOptions', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaFileRequest $request)
    {
        $file = $request->file('file');
        $folder = MediaFolder::findOrFail($request->input('folder_id'));

        $path = MediaFileHelper::storeUploadedFile($file, $folder);

        $dto = new MediaFileData(
            userId: auth()->id() ?? 1,
            file: $file,
            path: $path,
            mediaFolderId: $folder->id
        );

        $mediaFile = MediaFileHelper::createMediaFileFromDto($dto);
        MediaFileHelper::attachRandomTags($mediaFile);

        return redirect()->route('media-files.index')->with('success', 'Media đã được tải lên');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $file)
    {
        $view = $request->get('view');

        $fileData = MediaFile::findOrFail($file);
        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($fileData->folder->id);

        return view('media.files.file-modal')
            ->with(['file' => $fileData, 'breadcrumbs' => $breadcrumbs, 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaFile $file)
    {
        $userId = auth()->user()->id ?? null;

        $folders = MediaFolder::where('parent_id', null)->get();

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, $file->folder->id, 'media_file');

        $tags = MediaTag::all();

        // Lấy các tag ID đã gắn với image
        $selectedTags = $file->tags->pluck('id')->toArray();

        return view('media.files.edit', compact('file', 'folders', 'renderFolderOptions', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
