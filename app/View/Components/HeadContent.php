<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeadContent extends Component
{
    public string $titleContent;
    public ?string $viewMode;
    public array $routeAction;
    public array $buttonDropdown;
    public ?array $filters;
    public ?string $urlCurrent;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $titleContent = 'Button',
        ?string $viewMode = '',
        array $routeAction,
        array $buttonDropdown = [],
        ?string $urlCurrent = 'folder',
        ?array $filters = []
    )
    {
        $this->titleContent = $titleContent;
        $this->viewMode = $viewMode;
        $this->routeAction = $routeAction;
        $this->buttonDropdown = $buttonDropdown;
        $this->filters = $filters;
        $this->urlCurrent = $urlCurrent;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.head-content');
    }
}
