<?php

namespace App\Helpers;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\Session;

class MediaFolderHelper
{
    public function saveFoldersFromBreadcrumb(string $breadcrumb, int $userId): MediaFolder
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

    public function getBreadcrumbFromAnyFolder(MediaFolder $folder): string
    {
        $names = [];

        while ($folder) {
            array_unshift($names, $folder->name);
            $folder = $folder->parent;
        }

        return implode('/', $names);
    }

    public function buildBreadcrumb($folderId)
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

    public function buildFolderTree($folders = [], $depth = 0)
    {
        //
    }

    public static function renderFolderOptions(?int $userId, ?int $forderId = null, $prefix = '')
    {
        //  Get Folder
        $folders = new MediaFolder();

        if ($userId) {
            $folders = $folders->where('user_id', $userId);
        }

        $parentFolder = $folders->where('parent_id', null)->get();

        //  Render HTML
        $html = '';
        foreach ($parentFolder as $item) {
            $selected = '';
            if ($forderId && $item->id == $forderId) {
                $selected = 'selected';
            }

            $html .= '<option value="' . $item->id . '" ' . $selected . '>' . $prefix . '📁 ' . $item->name . '</option>';

            foreach ($item->children as $childItem) {
                if ($forderId && $childItem->id == $forderId) {
                    $selected = 'selected';
                }

                $html .= '<option value="' . $childItem->id . '" ' . $selected . '>' . $prefix . '-—' . $childItem->name . ' -> (' . $childItem->path . ')</option>';
            }
        }

        return $html;
    }

    public function getCurrentView($request)
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
