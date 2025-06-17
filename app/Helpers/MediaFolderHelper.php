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
