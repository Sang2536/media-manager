<?php

namespace App\View\Components;

use App\Models\MediaFile;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public MediaFile  $file;
    public string $viewMode;
    public array $routeAction;
    /**
     * Create a new component instance.
     */
    public function __construct(MediaFile $file, string $viewMode, array $routeAction)
    {
        $this->file = $file;
        $this->viewMode = $viewMode;
        $this->routeAction = $routeAction;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}
