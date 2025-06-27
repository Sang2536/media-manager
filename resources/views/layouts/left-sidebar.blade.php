{{-- Sidebar trái --}}
<aside class="w-1/4 bg-white border-r p-4 sticky top-0 h-screen flex flex-col justify-between">

    {{-- Nội dung menu ở trên --}}
    <div>
        <h2 class="text-xl font-bold mb-6">📁 Media Manager</h2>
        <nav class="space-y-2">
            <a href="{{ route('media-dashboard.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-100
                  {{ request()->routeIs('media-dashboard.*') ? 'bg-blue-200 font-semibold text-blue-800' : '' }}">
                🏠 Dashboard
            </a>

            <a href="{{ route('media-folders.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-100
                  {{ request()->routeIs('media-folders.*') ? 'bg-blue-200 font-semibold text-blue-800' : '' }}">
                📂 Quản lý thư mục
            </a>

            <a href="{{ route('media-files.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-100
                  {{ request()->routeIs('media-files.*') ? 'bg-blue-200 font-semibold text-blue-800' : '' }}">
                🖼️ Quản lý Media
            </a>

            <a href="{{ route('media-tags.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-100
                  {{ request()->routeIs('media-tags.*') ? 'bg-blue-200 font-semibold text-blue-800' : '' }}">
                🏷️ Quản lý Tags
            </a>

            <a href="{{ route('media-metadata.index') }}"
               class="block px-3 py-2 rounded hover:bg-blue-100
                  {{ request()->routeIs('media-metadata.*') ? 'bg-blue-200 font-semibold text-blue-800' : '' }}">
                📄 Metadata
            </a>
        </nav>
    </div>

    {{-- Vùng kéo thả xóa nhanh --}}
    <div id="quick-delete-area"
         class="mt-6 text-center p-3 rounded-lg border-2 border-dashed border-gray-300 text-gray-500 hover:border-red-500 hover:text-red-600 transition
                bg-gray-50"
         ondrop="handleDeleteDrop(event)"
         ondragover="event.preventDefault()"
         ondragenter="this.classList.add('bg-red-100')"
         ondragleave="this.classList.remove('bg-red-100')"
         title="Kéo thả file hoặc thư mục vào đây để xoá nhanh">
        🗑️ <div class="text-sm mt-1">Kéo vào đây để xoá</div>
    </div>
</aside>
