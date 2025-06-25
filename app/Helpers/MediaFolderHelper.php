<?php

namespace App\Helpers;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\Session;

class MediaFolderHelper
{
    public static function saveFoldersFromBreadcrumb(string $breadcrumb, int $userId): MediaFolder
    {
        $parts = explode('/', $breadcrumb);
        $parentId = null;
        $currentFolder = null;

        foreach ($parts as $name) {
            $currentFolder = MediaFolder::firstOrCreate(
                [
                    'user_id'   => $userId,
                    'name'      => $name,
                    'parent_id' => $parentId,
                ],
                [
                    'storage'   => 'local',
                ]
            );

            $parentId = $currentFolder->id;
        }

        return $currentFolder; // trả về folder cuối cùng (Firefly)
    }

    public static function getBreadcrumbFromAnyFolder(MediaFolder $folder): string
    {
        $names = [];

        while ($folder) {
            array_unshift($names, $folder->name);
            $folder = $folder->parent;
        }

        return implode('/', $names);
    }

    public static function buildBreadcrumb($folderId)
    {
        $breadcrumb = [];

        while ($folderId) {
            $folder = MediaFolder::find($folderId);
            if (!$folder) break;
            $breadcrumb[] = $folder;
            $folderId = $folder->parent_id;
        }

        return array_reverse($breadcrumb);
    }

    public static function buildFolderTree($folders = [], $depth = 0)
    {
        //
    }

    public static function renderFolderOptions(?int $userId = null, ?int $selectedFolderId = null, string $mode = 'media_file'): string
    {
        $query = MediaFolder::query()->with('children');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rootFolders = $query->whereNull('parent_id')->get();

        $html = '<label class="block font-semibold mb-1">📂 Thư mục</label>';
        $html .= '<select name="folder_id" class="w-full border rounded px-3 py-2">';
        $html .= '<option value="">-- Không có --</option>';

        foreach ($rootFolders as $folder) {
            $html .= self::renderFolderOptionItem($folder, $selectedFolderId, $mode);
        }

        $html .= '</select>';

        return $html;
    }

    protected static function renderFolderOptionItem($folder, ?int $selectedId = null, string $mode = 'media_file', string $prefix = ''): string
    {
        $selected = $selectedId === $folder->id ? 'selected' : '';
        $html = '<option value="' . e($folder->id) . '" ' . $selected . '>' . $prefix . '📁 ' . e($folder->name) . '</option>';

        foreach ($folder->children as $child) {
            $html .= self::renderFolderOptionItem($child, $selectedId, $mode, $prefix . '— ');
        }

        return $html;
    }

    public static function getCurrentView($request)
    {
        $view = $request->get('view');

        if ($view == 'list') {
            Session::put('view', 'list');
        } else {
            Session::put('view', 'grid');
        }

        return Session::get('view');
    }
}
