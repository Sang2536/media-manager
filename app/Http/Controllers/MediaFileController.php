<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFolderHelper;
use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    protected $mediaFolderHelper;

    public function __construct(MediaFolderHelper $mediaFolderHelper)
    {
        $this->mediaFolderHelper = $mediaFolderHelper;
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
        $folders = MediaFolder::where('parent_id', null)->get();
        $tags = MediaTag::all();

        return view('media.files.create', compact('folders', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaFileRequest $request)
    {
        $request->validate([
            'file' => $request->file(), // 10MB
            'original_name' => $request->original_name,
            'folder_id' => $request->folder_id,
            'tags' => $request->tags,
            'metadata' => $request->metadata,
        ]);

        $file = $request->file('file');
        $folderId = $request->input('folder_id');

        $originalName = $file->getClientOriginalName();

        $pathInfo = pathinfo($originalName, PATHINFO_FILENAME);     //  Tách tên file mà không có phần đuôi (extension)
        $normalized = \Normalizer::normalize($pathInfo, \Normalizer::FORM_KD);      //  normalize chuỗi về dạng chuẩn
        $filename = str()->slug($normalized) . '.' . $file->getClientOriginalExtension();       //  Chuyển thành dạng url

        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        $folder = MediaFolder::findOrFail($folderId);
        $folderPath = 'media/' . $folder->name;     //  url của folder chứa file
        $path = $file->storeAs($folderPath, $filename, 'public'); // lưu vào storage/app/public/media/{folder_id}

        $mediaFile = MediaFile::create([
            'user_id' => auth()->id() ?? 1,
            'filename' => $filename,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'path' => $path,
            'thumbnail_path' => null,
            'media_folder_id' => $folderId,
            'is_public' => true,
        ]);

        $tags = MediaTag::get();
        $mediaFile->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

        return redirect()->route('media-files.index')->with('success', 'Media đã được tải lên');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $file)
    {
        $view = $request->get('view');

        $fileData = MediaFile::findOrFail($file);
        $breadcrumbs = $this->mediaFolderHelper->buildBreadcrumb($fileData->folder->id);

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
        $optionSelect = $this->mediaFolderHelper->renderFolderOptions($userId, $file->folder->id);

        $tags = MediaTag::all();
        // Lấy các tag ID đã gắn với image
        $selectedTags = $file->tags->pluck('id')->toArray();

        return view('media.files.edit', compact('file', 'folders', 'optionSelect', 'tags', 'selectedTags'));
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
