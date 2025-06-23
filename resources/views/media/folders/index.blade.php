@extends('layouts.app')

@section('title', 'Folder Manager')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <x-head-content
            title-content="📁 Danh sách Media"
            :view-mode="$view"
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
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @forelse ($folders as $folder)
                    <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-[1.02] transition text-center">
                        <a href="{{ route('media-folders.index', ['parent' => $folder->id, 'view' => 'grid']) }}" class="block">
                            <div class="text-6xl mb-2">📁</div>
                            <div class="font-semibold text-lg truncate">{{ $folder->name }}</div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ $folder->children()->count() }} thư mục - {{ $folder->files()->count() }} ảnh
                            </div>
                        </a>
                        <div class="mt-3 flex justify-center gap-4 text-sm">
                            <button type="button" onclick="openModal('{{ route('media-folders.show', $folder->id) }}', '{{ $view }}')" class="text-green-600 hover:underline">
                                Xem
                            </button>

                            <a href="{{ route('media-folders.edit', $folder) }}" class="text-blue-600 hover:underline">Sửa</a>

                            <form action="{{ route('media-folders.destroy', $folder) }}" method="POST"
                                  onsubmit="return confirm('Xóa thư mục này?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Xóa</button>
                            </form>
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
                    :headers="['Folder', 'Folder con - Ảnh', 'Created at', 'Action']"
                >
                    {{-- Table body --}}
                    @forelse ($folders as $folder)
                        <tr class="hover:bg-gray-100 transition cursor-pointer"
                            onclick="window.location='{{ route('media-folders.index', ['parent' => $folder->id, 'view' => $view]) }}'">
                            <td class="px-6 py-4 flex items-center gap-2">
                                <span>📁</span>
                                <span>{{ $folder->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                Folder con: {{ $folder->children()->count() }} <br />
                                Ảnh: {{ $folder->files()->count() }}
                            </td>
                            <td class="px-6 py-4">{{ $folder->created_at }}</td>
                            <td class="px-6 py-4 flex items-center gap-4"
                                onclick="event.stopPropagation();"> {{-- ngăn chặn redirect khi bấm vào "Sửa" / "Xóa" --}}
                                <button type="button" onclick="openModal('{{ route('media-folders.show', $folder->id) }}', '{{ $view }}')" class="text-green-600 hover:underline">
                                    Xem
                                </button>

                                <a href="{{ route('media-folders.edit', $folder) }}" class="text-blue-600 hover:underline">Sửa</a>

                                <form action="{{ route('media-folders.destroy', $folder) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                                </form>
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
