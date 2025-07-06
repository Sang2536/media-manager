<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MetadataField extends Component
{
    public array $metadata;
    public ?int $limit;

    public function __construct(array $metadata = [], ?int $limit = 10)
    {
        $this->metadata = is_array($metadata) ? $metadata : $metadata->toArray();
        $this->limit = $limit;
    }

    public function render()
    {
        return view('components.metadata-field');
    }
}
