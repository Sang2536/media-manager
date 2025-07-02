<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\MediaFileData;
use App\Helpers\MediaFileHelper;
use App\Helpers\MediaFolderHelper;
use App\Helpers\ResponseHelper;
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
        try {
            $dto = MediaFileData::fromRequest($request);
            MediaFileHelper::handleUploadFromDto($dto);

            return ResponseHelper::result(true, '📤 Media đã được tải lên thành công', 200, route('media-files.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi khi tải lên: ' . $e->getMessage(), 400);
        }
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
    public function update(MediaFileRequest $request, MediaFile $file)
    {
        try {
            $dto = MediaFileData::fromRequest($request, $file);
            MediaFileHelper::handleUploadFromDto($dto, $file);

            return ResponseHelper::result(true, '✅ Media đã được cập nhật thành công.', 200, route('media-files.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi cập nhật: ' . $e->getMessage(), 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaFile $file)
    {
        $file->delete();

        return redirect()->route('media-files.index')
            ->with('success', '🗑️ Media đã được xoá thành công.');
    }
}
