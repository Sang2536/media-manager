<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFolderHelper;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:5120', // 5MB
            'folder_id' => 'required|exists:media_folders,id',
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
    public function edit(string $id)
    {
        //
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
