<?php

namespace App\Helpers;

use App\Models\MediaFolder;
use Illuminate\Support\Facades\Session;

class MediaFolderHelper
{
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

    public static function renderFolderOptions($userId, $prefix = '')
    {
        $folders = MediaFolder::where('user_id', $userId)->get();

        $html = '';

        foreach ($folders as $folder) {
            if (! $folder->parent_id)
                $html .= '<option value="' . $folder->id . '">' . $prefix . '📁 ' . $folder->name . '</option>';
            else
                $html .= '<option value="' . $folder->id . '">' . $prefix . '— ' . $folder->name . '</option>';
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
