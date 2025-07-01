<nav class="mb-6 text-sm flex items-center flex-wrap text-gray-700">
    {{-- Link về Root --}}
    <a href="{{ route('media-folders.index', ['view' => $viewMode]) }}"
       class="text-blue-600 hover:underline flex items-center gap-1">
        Root
    </a>

    {{-- Nếu là mảng -> foreach, nếu là chuỗi -> nối luôn --}}
    @if (is_array($breadcrumbs))
        @foreach ($breadcrumbs as $crumb)
            <span class="mx-2 text-gray-400">{{ $separate }}</span>
            <a href="{{ route('media-folders.index', ['parent' => $crumb->id, 'view' => $viewMode]) }}"
               class="text-blue-600 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h4l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                </svg>
                {{ $crumb->name }}
            </a>
        @endforeach
    @elseif (is_string($breadcrumbs) && $breadcrumbs !== '')
        <span class="mx-2 text-gray-400">{{ $separate }}</span>
        <span class="text-gray-600">{{ $breadcrumbs }}</span>
    @endif

    {{-- Folder hiện tại (nếu có) sẽ (không có link) --}}
    @if ($current)
        <span class="mx-2 text-gray-400">{{ $separate }}</span>
        <span class="text-gray-500 flex items-center gap-1">
            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h4l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
            </svg>
            {{ $current }}
        </span>
    @endif
</nav>
