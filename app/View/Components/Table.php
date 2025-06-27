<?php

namespace App\View\Components;

use App\Models\MediaFile;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    public array $headers;
    public ?int $numberOfColumns;
    public ?string $viewMode;

    /**
     * Create a new component instance.
     */
    public function __construct(array $headers, ?int $numberOfColumns, ?string $viewMode)
    {
        $this->headers = $headers;
        $this->numberOfColumns = $numberOfColumns;
        $this->viewMode = $viewMode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table');
    }
}
