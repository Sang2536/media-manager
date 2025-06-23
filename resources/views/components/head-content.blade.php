<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        {{ $titleContent }}
    </h1>

    <div class="flex items-center justify-end flex-wrap gap-4 mb-8">
        {{-- Nhóm nút chuyển đổi view --}}
        @if ($viewMode)
            <div class="flex gap-2">
                <a href="{{ $routeAction['mode'] }}"
                   class="px-4 py-2 rounded-lg font-medium transition border
                   {{ $viewMode === 'grid'
                       ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                       : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                    🟦 Lưới
                </a>
                <a href="{{ $routeAction['mode'] }}"
                   class="px-4 py-2 rounded-lg font-medium transition border
                   {{ $viewMode === 'list'
                       ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                       : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                    📋 Danh sách
                </a>
            </div>
        @endif

        <hr class="my-2 border-t border-gray-300">

        {{-- Nhóm nút chức năng --}}
        <div class="flex gap-2">
            <x-button
                class="inline-flex items-center gap-2 border border-yellow-500 bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition shadow"
                name-btn="🧹 Bộ lọc"
            />

            <x-button
                class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow"
                name-btn="🔄 Làm mới"
            />

            <x-button
                :href="$routeAction['create']"
                class="inline-flex items-center gap-2 border border-green-600 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow"
                name-btn="➕ Thêm"
            />

            <x-button
                :href="$routeAction['destroy']"
                class="inline-flex items-center gap-2 border border-red-600 bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition shadow"
                name-btn="🗑️ Xóa"
            />

            @if($buttonDropdown)
                <x-dropdown
                    :button-title="$buttonDropdown['title']"
                    :button-icon="true"
                    type="menu"
                    :items="$buttonDropdown['items']"
                />
            @endif
        </div>
    </div>
</div>
