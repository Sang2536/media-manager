<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        {{ $titleContent }}
    </h1>

    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
        {{-- Nhóm nút chuyển đổi view --}}
        <div class="flex gap-2">
            <a href="{{ route('media-tags.index', ['view' => 'grid']) }}"
               class="px-4 py-2 rounded-lg font-medium transition border
               {{ $view === 'grid'
                   ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                   : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                🟦 Lưới
            </a>
            <a href="{{ route('media-tags.index', ['view' => 'list']) }}"
               class="px-4 py-2 rounded-lg font-medium transition border
               {{ $view === 'list'
                   ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                   : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                📋 Danh sách
            </a>
        </div>

        {{-- Nhóm nút chức năng --}}
        <div class="flex gap-2">
            <x-button :id="'btn-filter'"
                      :class="'inline-flex items-center gap-2 border border-yellow-500 bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition shadow'"
                      :type="'button'"
                      :nameBtn="'🧹 Bộ lọc'"
            />
            <button type="button"
                    class="inline-flex items-center gap-2 border border-yellow-500 bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition shadow">
                🧹 Bộ lọc
            </button>
            <button type="button"
                    class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow">
                🔄 Làm mới
            </button>
            <a href="{{ $routeAction['create'] }}"
               class="inline-flex items-center gap-2 border border-green-600 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow">
                ➕ Thêm
            </a>
            <a href="{{ $routeAction['destroy'] }}"
               class="inline-flex items-center gap-2 border border-red-600 bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition shadow">
                🗑️ Xóa
            </a>

            <!-- Nút Dropdown -->
            <div class="relative">
                <button id="dropdownBtn"
                        class="inline-flex items-center gap-2 border border-gray-400 bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition shadow">
                    ⚙️ Tác vụ khác
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="dropdownMenu"
                     class="absolute right-0 z-50 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg p-2 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🔄 Làm mới</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📥 Tải xuống</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📂 Chuyển thư mục</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">✅ Chọn tất cả</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🧾 Chi tiết</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🖼 Xem nhanh</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📌 Ghim</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📤 Xuất ảnh</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">⚙️ Cài đặt hiển thị</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">♿ Chế độ truy cập</a>
                </div>
            </div>
        </div>
    </div>
</div>
