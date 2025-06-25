<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFolderHelper;
use App\Models\MediaFolder;
use Illuminate\Http\Request;

class MediaFolderController extends Controller
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
        $parentId = $request->get('parent');

        $folders = MediaFolder::where('parent_id', $parentId)->paginate(12);
        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($parentId);

        return view('media.folders.index', compact('folders', 'view', 'breadcrumbs', 'parentId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->user()->id ?? null;

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, null, 'media_folder');

        return view('media.folders.create')->with('renderFolderOptions', $renderFolderOptions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:media_folders,name|max:255',
        ]);

        MediaFolder::create(['name' => $request->name]);

        return redirect()->route('media-folders.index')->with('success', 'Thư mục đã được tạo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $folder)
    {
        $view = $request->get('view');

        $folderData = MediaFolder::withCount(['children', 'files'])->findOrFail($folder);
        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folderData->parent_id);

        return view('media.folders.show')
            ->with(['folder' => $folderData, 'breadcrumbs' => $breadcrumbs, 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaFolder $folder)
    {
        $userId = auth()->user()->id ?? null;

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folder->parent_id);
        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, $folder->parent_id, 'media_folder');

        return view('media.folders.edit', compact('folder', 'breadcrumbs', 'renderFolderOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MediaFolder $folder)
    {
        $request->validate([
            'name' => 'required|max:255|unique:media_folders,name,' . $folder->id,
        ]);

        $folder->update(['name' => $request->name]);

        return redirect()->route('media-folders.index')->with('success', 'Đã cập nhật thư mục');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaFolder $folder)
    {
        if ($folder->files()->count() > 0) {
            return back()->withErrors('Không thể xóa thư mục đang chứa file');
        }

        $folder->delete();

        return redirect()->route('media-folders.index')->with('success', 'Đã xóa thư mục');
    }
}
