<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $idModalWrapper;
    public string $idModalContent;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $idModalWrapper = 'modalWrapper',
        string $idModalContent = 'modalContent'
    )
    {
        $this->idModalWrapper = $idModalWrapper;
        $this->idModalContent = $idModalContent;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
