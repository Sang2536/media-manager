<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Collapse extends Component
{
    public string $id;
    public ?string $class;
    public string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $id, ?string $class = null, string $title)
    {
        $this->id = $id;
        $this->class = $class;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.collapse');
    }
}
