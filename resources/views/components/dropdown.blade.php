<div class="relative inline-block">
    <!-- Nút Dropdown -->
    <x-button
        id="{{ $id = $attributes->get('id') ?? 'dropdownBtn-' . uniqid() }}"
        class="dropdown-toggle inline-flex items-center gap-2 border border-gray-400 bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition shadow"
        type="button"
        :name-btn="$buttonTitle"
        data-toggle="dropdown"
        data-target="dropdownMenu-{{ $id }}"
    >
        @if ($buttonIcon)
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        @endif
    </x-button>

    <!-- Menu Dropdown -->
    <div
        id="dropdownMenu-{{ $id }}"
        class="dropdown-menu absolute right-0 z-50 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg p-2 hidden"
    >
        @if ($type === 'menu')
            @foreach ($items as $item)
                <a href="{{ $item['href'] ?? '#' }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    {{ $item['text'] ?? 'No text' }}
                </a>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>
</div>
