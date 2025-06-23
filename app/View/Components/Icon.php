<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;       // Tên icon
    public string $set;        // Bộ icon (svg/fa/hero)
    public string $class;      // Class bổ sung

    public function __construct(string $name, string $set = 'svg', string $class = '')
    {
        $this->name = $name;
        $this->set = $set;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.icon');
    }
}
