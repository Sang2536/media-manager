<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filter extends Component
{
    public string $formId;
    public string $formAction;
    public string $formMethod;
    public ?array $filters;
    public ?string $typeFilter;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $formId = 'filter-form',
        string $formAction = '',
        string $formMethod = 'GET',
        array $filters = [],
        ?string $typeFilter = 'folder'
    )
    {
        $this->formId = $formId;
        $this->formAction = $formAction ?: request()->url();
        $this->formMethod = strtoupper($formMethod);
        $this->filters = $filters;
        $this->typeFilter = $typeFilter;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter');
    }
}
