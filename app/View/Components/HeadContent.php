<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeadContent extends Component
{
    public string $titleContent = 'Button';
    public ?string $viewMode = '';
    public array $routeAction;
    public array $buttonDropdown;


    /**
     * Create a new component instance.
     */
    public function __construct(
        string $titleContent = 'Title Content',
        ?string $viewMode,
        array $routeAction,
        array $buttonDropdown = []
    )
    {
        $this->titleContent = $titleContent;
        $this->viewMode = $viewMode;
        $this->routeAction = $routeAction;
        $this->buttonDropdown = $buttonDropdown;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.head-content');
    }
}
