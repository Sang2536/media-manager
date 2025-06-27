<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class TabbedFolderEditor extends Component
{
    public string $breadcrumbPath;
    public string $renderFolderOptions;
    public string $folderName;
    public string $mode;

    /**
     * @param string $breadcrumbPath Giá trị hiển thị trong input breadcrumb
     * @param string $renderFolderOptions HTML từ renderFolderOptions()
     * @param string $mode 'create' hoặc 'edit'
     */
    public function __construct(string $breadcrumbPath = '', string $renderFolderOptions = '', ?string $folderName = '', string $mode = 'edit')
    {
        $this->breadcrumbPath = $breadcrumbPath;
        $this->renderFolderOptions = $renderFolderOptions;
        $this->folderName = $folderName;
        $this->mode = $mode;
    }

    public function render(): View|Closure|string
    {
        return view('components.tabbed-folder-editor');
    }
}
