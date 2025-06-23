@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                📁 Danh sách Media
            </h1>

            <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                @php $view = request()->get('view', 'grid'); @endphp

                {{-- Nhóm nút chuyển đổi view --}}
                <div class="flex gap-2">
                    <a href="{{ route('media-files.index', ['view' => 'grid']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition border
               {{ $view === 'grid'
                   ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                   : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                        🟦 Lưới
                    </a>
                    <a href="{{ route('media-files.index', ['view' => 'list']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition border
               {{ $view === 'list'
                   ? 'bg-indigo-600 text-white border-indigo-600 shadow hover:bg-indigo-700'
                   : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}">
                        📋 Danh sách
                    </a>
                </div>

                {{-- Nhóm nút chức năng --}}
                <div class="flex gap-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 border border-yellow-500 bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition shadow">
                        🧹 Bộ lọc
                    </button>
                    <button type="button"
                            class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow">
                        🔄 Làm mới
                    </button>
                    <a href="{{ route('media-files.create') }}"
                       class="inline-flex items-center gap-2 border border-green-600 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow">
                        ➕ Thêm
                    </a>
                    <a href="{{ route('media-files.create') }}"
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


        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($mediaFiles as $file)
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition hover:scale-[1.02] duration-300">
{{--                <img src="{{ asset('storage/' . $file->thumbnail_path ?? $file->path) }}"--}}
                    <img src="{{ $file->path }}"
                         alt="{{ $file->original_name }}"
                         class="w-full h-48 object-cover" />

                    <div class="p-4">
                        <h2 class="text-lg font-semibold truncate">{{ $file->original_name }}</h2>

                        <p class="text-sm text-gray-500 mt-1">
                            📂 {{ $file->folder->name ?? 'Không có' }}<br>
                            👤 {{ $file->user->name }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($file->tags as $tag)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $tag->name }}</span>
                            @endforeach
                        </div>

                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            @foreach ($file->metadata as $meta)
                                <div>🔹 <strong>{{ $meta->key }}:</strong> {{ $meta->value }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $mediaFiles->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
