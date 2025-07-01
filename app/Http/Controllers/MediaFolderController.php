<?php

namespace App\Http\Controllers;

use App\Helpers\MediaFolderHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\MediaFolderRequest;
use App\Models\MediaFolder;
use Illuminate\Http\Request;

class MediaFolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'list');

        $parentId = $request->get('parent');

        $folders = MediaFolderHelper::getFoldersByParent($parentId, $request->all());

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($parentId, false, '▸');

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
        $userId = auth()->id() ?? 1;
        $name = $request->folderName();
        $parentId = $request->validatedParentId($userId);
        $actionBreadcrumb = $request->get('action');

        if (! $name) {
            return ResponseHelper::result(false, 'Vui lòng nhập tên thư mục.');
        }

        try {
            if ($request->get('active_tab') ==='breadcrumb') {
                MediaFolderHelper::saveFromBreadcrumb($name, $userId, $parentId, $actionBreadcrumb);
            } else {
                $dto = $request->toDto($userId);
                MediaFolderHelper::saveSingle($dto);
            }

            return ResponseHelper::result(true, 'Tạo thư mục thành công!', 201, route('media-folders.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Có lỗi xảy ra: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, MediaFolder $folder)
    {
        $view = $request->get('view');

        $folderData = $folder->loadCount(['children', 'files']);

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folderData->parent_id, false, '▸');

        $count = MediaFolderHelper::countAllDescendants($folder);

        return view('media.folders.show')->with([
            'folder' => $folderData,
            'breadcrumbs' => $breadcrumbs,
            'view' => $view,
            'count' => $count,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaFolder $folder)
    {
        $userId = auth()->id();

        $breadcrumbs = MediaFolderHelper::buildBreadcrumb($folder->parent_id);
        $breadcrumbPath = MediaFolderHelper::buildBreadcrumb($folder->id, true, "▸");

        $renderFolderOptions = MediaFolderHelper::renderFolderOptions($userId, $folder->parent_id, 'media_folder');

        return view('media.folders.edit', compact('folder', 'breadcrumbs', 'breadcrumbPath', 'renderFolderOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaFolderRequest $request, MediaFolder $folder)
    {
        $userId = auth()->id() ?? 1;

        // Kiểm tra quyền sở hữu
        if (! MediaFolderHelper::isOwnedByUser($folder, $userId)) {
            return ResponseHelper::result(false, 'Bạn không có quyền cập nhật thư mục này.', 403);
        }

        $name = $request->folderName();
        $parentId = $request->validatedParentId($userId);
        $actionBreadcrumb = $request->get('action');

        try {
            if ($request->get('active_tab') ==='breadcrumb') {
                MediaFolderHelper::saveFromBreadcrumb($name, $userId, $parentId, $folder, $actionBreadcrumb);
            } else {
                $dto = $request->toUpdateDto($folder, $userId);
                MediaFolderHelper::saveSingle($dto, $folder);
            }

            return ResponseHelper::result(true, 'Cập nhật thư mục thành công.', 200, route('media-folders.index'));
        } catch (\Throwable $e) {
            return ResponseHelper::result(false, 'Lỗi cập nhật: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaFolder $folder)
    {
        $userId = auth()->id() ?? 1;

        return MediaFolderHelper::deleteFolder($folder, $userId);
    }
}
