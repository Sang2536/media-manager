<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFolderHelper;
use App\Http\Requests\MediaFolderRequest;
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

//        dd($request->all());
        $folders = MediaFolderHelper::getFoldersByParent($parentId, $request->all());

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($parentId);

        $filters = $request->all();

        return view('media.folders.index', compact('folders', 'view', 'breadcrumbs', 'parentId', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->id();

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, null, 'media_folder');

        return view('media.folders.create')->with('renderFolderOptions', $renderFolderOptions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaFolderRequest $request)
    {
        $userId = auth()->id();

        $rootFolder = MediaFolderHelper::getRootFolder($userId);
        if (! $rootFolder) {
            return redirect()->back()->withErrors(['root' => 'Không tìm thấy thư mục gốc của bạn.']);
        }

        $parentId = $request->input('parent_id') ?? $rootFolder->id;
        if (! MediaFolderHelper::isDescendantOf($parentId, $rootFolder->id)) {
            $parentId = $rootFolder->id;
        }

        $name = $request->input('breadcrumb_path') ?? $request->input('folder_name');

        if (! $name) {
            return redirect()->back()->withErrors(['name' => 'Vui lòng nhập tên thư mục.']);
        }

        $isBreadcrumb = str_contains($name, '/');

        try {
            if ($isBreadcrumb) {
                MediaFolderHelper::saveFromBreadcrumb($name, $userId, $parentId);
            } else {
                MediaFolderHelper::saveSingle($name, $userId, $parentId);
            }

            return redirect()->route('media-folders.index')->with('success', 'Tạo thư mục thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->with('active_tab', $request->input('active_tab'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $folder)
    {
        $view = $request->get('view');

        $folderData = MediaFolder::withCount(['children', 'files'])->findOrFail($folder, true);
        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folderData->parent_id);

        return view('media.folders.show')
            ->with(['folder' => $folderData, 'breadcrumbs' => $breadcrumbs, 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaFolder $folder)
    {
        $userId = auth()->id();

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folder->parent_id);
        $breadcrumbPath = MediaFolderHelper::buildBreadcrumb($folder->id, true);

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, $folder->parent_id, 'media_folder');

        return view('media.folders.edit', compact('folder', 'breadcrumbs', 'breadcrumbPath', 'renderFolderOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaFolderRequest $request, MediaFolder $folder)
    {
        $userId = auth()->id();

        // Luôn đảm bảo user có root folder
        $rootFolder = MediaFolderHelper::getRootFolder($userId);
        if (! $rootFolder) {
            return redirect()->back()->withErrors(['root' => 'Không tìm thấy thư mục gốc của bạn.']);
        }

        // Ưu tiên parent_id từ request, nếu không có thì dùng root
        $parentId = $request->input('parent_id') ?? $rootFolder->id;

        // Không cho phép đặt folder ra ngoài nhánh root
        if (! MediaFolderHelper::isDescendantOf($parentId, $rootFolder->id)) {
            $parentId = $rootFolder->id;
        }

        // Ưu tiên lấy tên từ breadcrumb hoặc folder_name
        $name = $request->input('breadcrumb_path') ?? $request->input('folder_name');

        if (! $name) {
            return redirect()->back()->withErrors(['name' => 'Tên thư mục không được bỏ trống.']);
        }

        // Nếu là breadcrumb
        $isBreadcrumb = str_contains($name, '/');

        try {
            if ($isBreadcrumb) {
                // ✅ Xử lý cập nhật thông qua cây breadcrumb
                MediaFolderHelper::saveFromBreadcrumb($name, $userId, $parentId, $folder);
            } else {
                // ✅ Cập nhật tên & parent trực tiếp
                MediaFolderHelper::updateFolderInfo($folder, $name, $parentId);
            }

            return redirect()->route('media-folders.index')->with('success', 'Cập nhật thư mục thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaFolder $folder)
    {
        $userId = auth()->id();

        // Kiểm tra quyền sở hữu
        if (! MediaFolderHelper::isOwnedByUser($folder, $userId)) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Bạn không có quyền xoá thư mục này.'], 403);
            }

            return redirect()->back()->withErrors(['unauthorized' => 'Bạn không có quyền xoá thư mục này.']);
        }

        // Không cho xoá nếu đang chứa file
        if ($folder->files()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Không thể xoá thư mục đang chứa file.'], 400);
            }

            return back()->withErrors('Không thể xoá thư mục đang chứa file');
        }

        $folder->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Đã xoá thư mục']);
        }

        return redirect()->route('media-folders.index')->with('success', 'Đã xoá thư mục');
    }
}
