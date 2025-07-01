<?php

namespace App\View\Components;

use App\Models\MediaFolder;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public iterable|string $breadcrumbs;
    public ?string $viewMode;
    public array $routeAction;

    public ?string $current;

    public iterable|string $separate;

    /**
     * Create a new component instance.
     */
    public function __construct(
        iterable|string $breadcrumbs,
        ?string $viewMode = 'grid',
        array $routeAction,
        ?string $current = '',
        iterable|string $separate = '/'
    )
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->viewMode = $viewMode;
        $this->routeAction = $routeAction;
        $this->current = $current;
        $this->separate = $separate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
