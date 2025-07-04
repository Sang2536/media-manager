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
            @if ($viewMode)
                <x-button
                    id="{{ 'collapseFilter-' . uniqid() }}"
                    class="inline-flex items-center gap-2 border border-yellow-500 bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition shadow"
                    name-btn="🧹 Bộ lọc"
                    data-toggle="collapse"
                    data-target="#collapse-filter"
                />
            @endif

            <x-button
                class="inline-flex items-center gap-2 border border-cyan-500 bg-cyan-500 text-white px-5 py-2 rounded-lg hover:bg-cyan-600 transition shadow"
                name-btn="🔄 Làm mới"
                onclick="window.location.href = window.location.origin + window.location.pathname"
            />

            @if(isset($routeAction['modalCreate']) && $routeAction['modalCreate'] = true)
                <x-button
                    onclick="openModal('{{ $routeAction['create'] }}')"
                    class="inline-flex items-center gap-2 border border-green-600 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow"
                    name-btn="➕ Thêm"
                />
            @else
                <x-button
                    :href="$routeAction['create']"
                    class="inline-flex items-center gap-2 border border-green-600 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow"
                    name-btn="➕ Thêm"
                />
            @endif

            @if($buttonDropdown)
                <x-dropdown
                    :button-title="$buttonDropdown['title']"
                    :button-icon="true"
                    type="menu"
                    :items="$buttonDropdown['items']"
                    data-toggle="dropdown"
                    data-target=".dropdown-menu"
                />
            @endif
        </div>
    </div>

    <x-collapse
        id="collapse-filter"
        class="hidden items-center justify-end flex-wrap gap-4 mb-8"
        title="Filter">
        <x-filter :filters="$filters" :type-filter="$urlCurrent" />
    </x-collapse>
</div>
