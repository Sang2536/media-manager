<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public ?string $href;
    public string $tag;
    public ?string $type;
    public ?string $id;
    public ?string $class;
    public ?string $iconLeft;
    public ?string $iconRight;
    public ?string $nameBtn;

    public function __construct(
        string $nameBtn = null,
        ?string $href = null,
        string $type = 'button',
        string $id = null,
        string $class = null,
        string $iconLeft = null,
        string $iconRight = null
    ) {
        $this->nameBtn = $nameBtn;
        $this->href = $href;
        $this->tag = $href ? 'a' : 'button';
        $this->type = $this->tag === 'button' ? $type : null;
        $this->id = $id;
        $this->class = $class;
        $this->iconLeft = $iconLeft;
        $this->iconRight = $iconRight;
    }

    public function render()
    {
        return view('components.button');
    }
}
