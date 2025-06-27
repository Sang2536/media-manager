@extends('layouts.app')

@section('title', 'Folder Manager')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <x-head-content
            title-content="📁 Danh sách Media"
            :view-mode="$view"
            :filters="$filters"
            :route-action="[
                    'mode' => route('media-folders.index', ['view' => $view === 'grid' ? 'list' : 'grid']),
                    'create' => route('media-folders.create'),
                    'destroy' => '#',
                ]"
            :button-dropdown="[
                    'title' => '⚙️ Tác vụ khác',
                    'items' => [
                        ['href' => '#', 'text' => '🔄 Làm mới'],
                        ['href' => '#', 'text' => '📂 Chuyển thư mục'],
                        ['href' => '#', 'text' => '📥 Tải xuống'],
                        ['href' => '#', 'text' => '✅ Chọn tất cả'],
                        ['href' => '#', 'text' => '🧾 Chi tiết'],
                        ['href' => '#', 'text' => '🖼 Xem nhanh'],
                        ['href' => '#', 'text' => '📌 Ghim'],
                        ['href' => '#', 'text' => '📤 Xuất ảnh'],
                        ['href' => '#', 'text' => '⚙️ Cài đặt hiển thị'],
                        ['href' => '#', 'text' => '♿ Chế độ truy cập'],
                    ],
                ]"
        />

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="mb-6 text-green-700 bg-green-100 p-4 rounded-lg shadow">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 text-red-700 bg-red-100 p-4 rounded-lg shadow">{{ $errors->first() }}</div>
        @endif

        {{-- Breadcrumb --}}
        @if ($breadcrumbs)
            <x-breadcrumb
                :breadcrumbs="$breadcrumbs"
                :view-mode="$view"
                :route-action="[
                    'index' => route('media-folders.index')
                ]"
            />
        @endif

        @if ($view === 'grid')
            {{-- Grid view --}}
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($folders as $folder)
                    <div
                        draggable="true"
                        ondragstart="event.dataTransfer.setData('text/plain', '{{ route('media-folders.destroy', $folder->id) }}')"
                        class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-[1.02] transition text-center"
                    >
                        <a href="{{ route('media-folders.index', ['parent' => $folder->id, 'view' => 'grid']) }}" class="block">
                            <div class="text-6xl mb-2">📁</div>
                            <div class="font-semibold text-lg truncate">{{ $folder->name }}</div>
                            <div class="my-2">👤 {{ $folder->user->name }}</div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ $folder->children()->count() }} thư mục - {{ $folder->files()->count() }} ảnh
                            </div>
                        </a>
                        <div class="mt-2 flex justify-between gap-2 text-sm">
                            <button onclick="openModal('{{ route('media-folders.show', $folder->id) }}', '{{ $view }}')"
                                    class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                                Xem
                            </button>

                            <a href="{{ route('media-folders.edit', $folder->id) }}"
                               class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                                Sửa
                            </a>

                            <x-button
                                class="flex-1 text-center text-red-700 border border-red-500 bg-red-100 hover:bg-red-200 px-2 py-1 rounded min-w-0"
                                style="line-height: 1;"
                                name-btn="Xóa"
                                type="button"
                                onclick="handleDelete('{{ route('media-folders.destroy', $folder->id) }}')"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-6">Không có thư mục.</div>
                @endforelse
            </div>
        @else
            {{-- List view --}}
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <x-table
                    :view-mode="$view"
                    :headers="['Folder', 'User', 'Folder con - Ảnh', 'Created at', 'Action']"
                >
                    {{-- Table body --}}
                    @forelse ($folders as $folder)
                        <tr class="hover:bg-gray-100 transition cursor-pointer"
                            onclick="window.location='{{ route('media-folders.index', ['parent' => $folder->id, 'view' => $view]) }}'">
                            <td class="px-6 py-4 flex items-center gap-2">
                                <span>📁</span>
                                <span>{{ $folder->name }}</span>
                            </td>
                            <td class="px-6 py-4">👤 {{ $folder->user->name }}</td>
                            <td class="px-6 py-4">
                                📁 Folder con: {{ $folder->children()->count() }} <br />
                                🖼️ Ảnh: {{ $folder->files()->count() }}
                            </td>
                            <td class="px-6 py-4">{{ $folder->created_at }}</td>
                            <td class="px-6 py-4 flex items-center gap-4"
                                onclick="event.stopPropagation();"> {{-- ngăn chặn redirect khi bấm vào "Sửa" / "Xóa" --}}
                                <div class="mt-2 flex justify-between gap-2 text-sm">
                                    <button onclick="openModal('{{ route('media-folders.show', $folder->id) }}', '{{ $view }}')"
                                            class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                                        Xem
                                    </button>

                                    <a href="{{ route('media-folders.edit', $folder->id) }}"
                                       class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                                        Sửa
                                    </a>

                                    <x-button
                                        class="flex-1 text-center text-red-700 border border-red-500 bg-red-100 hover:bg-red-200 px-2 py-1 rounded min-w-0"
                                        style="line-height: 1;"
                                        name-btn="Xóa"
                                        type="button"
                                        onclick="handleDelete('{{ route('media-folders.destroy', $folder->id) }}')"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có thư mục nào.</td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $folders->appends(['view' => $view])->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal wrapper -->
    <div id="wrapperModal" class="hidden relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen">
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <button onclick="closeModal()"
                        class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                <div id="modalContent" class="text-gray-800">
                    Đang tải...
                </div>
            </div>
        </div>
    </div>
@endsection
