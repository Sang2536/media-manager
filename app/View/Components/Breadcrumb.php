<?php

namespace App\View\Components;

use App\Models\MediaFolder;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public iterable|string $breadcrumbs;
    public string $viewMode;
    public array $routeAction;

    public ?string $current;

    /**
     * Create a new component instance.
     */
    public function __construct(iterable|string $breadcrumbs, string $viewMode, array $routeAction, ?string $current = '')
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->viewMode = $viewMode;
        $this->routeAction = $routeAction;
        $this->current = $current;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
