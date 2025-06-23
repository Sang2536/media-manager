<?php
namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class Tag extends Component
{
    public string $id;
    public string $color;
    public bool $deletable;
    public int|string|null $tagId;
    public string $tagName;

    protected array $colors = [
        'amber', 'blue', 'cyan', 'emerald',
        'fuchsia', 'gray', 'green', 'indigo',
        'lime', 'orange', 'pink', 'purple',
        'red', 'rose',
    ];

    public function __construct(
        string $tagName,
        int|string|null $tagId = null,
        bool $deletable = false,
        ?string $color = null
    ) {
        $this->tagName = $tagName;
        $this->tagId = $tagId;
        $this->deletable = $deletable;

        // Tạo ID slug từ tagId hoặc tagName
        $this->id = Str::slug($tagId ?: $tagName);

        // Gán màu ngẫu nhiên nếu không có
        $this->color = $color ?? $this->colors[crc32($tagName) % count($this->colors)];
    }

    public function render(): View|Closure|string
    {
        return view('components.tag');
    }
}
