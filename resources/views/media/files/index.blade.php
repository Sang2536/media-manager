@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
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

        {{-- Media files --}}
        @if ($view === 'grid')
            {{-- Grid view --}}
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($mediaFiles as $file)
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition hover:scale-[1.02] duration-300">
                        <img src="{{ $file->image_url }}"
                             alt="{{ $file->original_name }}"
                             class="w-full h-48 object-cover" />

                        <div class="p-4">
                            <h2 class="text-lg font-semibold truncate">{{ str()->afterLast($file->filename, '/') }}</h2>

                            <p class="text-sm text-gray-500 mt-1">
                                📂 {{ $file->folder->name ?? 'Không có' }}<br>
                                👤 {{ $file->user->name }}
                            </p>

                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach ($file->tags as $tag)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4">
                            {{-- Copy URL --}}
                            <div class="mt-4 flex justify-between items-center text-xs border border-gray-300 bg-gray-50 rounded">
                                <div class="ml-2 truncate text-gray-700 w-4/6" title="{{ asset($file->path) }}">
                                    {{ asset($file->path) }}
                                </div>
                                <button onclick="copyToClipboard('{{ asset($file->path) }}')"
                                        class="ml-2 border border-gray-300 hover:bg-gray-300 font-semibold rounded-none px-2 py-2 text-xs">
                                    📋 Copy
                                </button>
                            </div>

                            <div class="mt-2 flex justify-between gap-2 text-sm">
                                <button onclick="openFileInfoModal({{ $file->id }}, '{{ $view }}')"
                                        class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                                    👁 Xem
                                </button>

                                <a href="{{ route('media-files.edit', $file->id) }}"
                                   class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                                    📝 Sửa
                                </a>

                                <form action="{{ route('media-files.destroy', $file->id) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa?')" class="flex-1 min-w-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full text-center text-red-700 border border-red-500 bg-red-100 hover:bg-red-200 px-2 py-1 rounded">
                                        ❌ Xóa
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-6">Không có thư mục.</div>
                @endforelse
            </div>
        @else
            {{-- List view --}}
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <table class="divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Media</th>
                        <th class="px-6 py-3">Filename</th>
                        <th class="px-6 py-3">Ngày tạo</th>
                        <th class="px-6 py-3">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($mediaFiles as $file)
                        <tr class="hover:bg-gray-100 justify-center transition">
                            <td class="px-6 py-4 items-center gap-2">
                                <img src="{{ $file->image_url }}"
                                     alt="{{ $file->original_name }}"
                                     class="w-full h-12 object-cover" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex text-sm text-gray-500 my-2">
                                    <div class="ml-2 w-1/2">
                                        📂 {{ $file->folder->name ?? 'Không có' }}
                                    </div>
                                    <div class="ml-2">
                                        👤 {{ $file->user->name }}
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-xs border border-gray-300 bg-gray-50 rounded">
                                    <div class="ml-2 truncate text-gray-700 w-4/6" title="{{ asset($file->path) }}">
                                        {{ asset($file->path) }}
                                    </div>
                                    <button onclick="copyToClipboard('{{ asset($file->path) }}')"
                                            class="ml-2 border border-gray-300 hover:bg-gray-300 font-semibold rounded-none px-2 py-2 text-xs">
                                        📋 Copy
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $file->created_at }}</td>
                            <td class="px-6 py-4 flex items-center gap-4"
                                onclick="event.stopPropagation();"> {{-- ngăn chặn redirect khi bấm vào "Sửa" / "Xóa" --}}
                                <div class="mt-2 flex justify-between gap-2 text-sm">
                                    <button onclick="openFileInfoModal({{ $file->id }}, '{{ $view }}')"
                                            class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                                        Xem
                                    </button>

                                    <a href="{{ route('media-files.edit', $file->id) }}"
                                       class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                                        Sửa
                                    </a>

                                    <form action="{{ route('media-files.destroy', $file->id) }}" method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa?')" class="flex-1 min-w-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full text-center text-red-700 border border-red-500 bg-red-100 hover:bg-red-200 px-2 py-1 rounded">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có thư mục nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-6">
            {{ $mediaFiles->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal Show Media wrapper -->
    <div id="fileModal" class="hidden relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen">
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <button onclick="closeFileInfoModal()"
                        class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                <div id="fileModalContent" class="text-gray-800">
                    Đang tải...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{--  JS Modal Show Media  --}}
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
                .then(() => alert("Đã copy URL vào clipboard!"))
                .catch(err => alert("Copy thất bại: " + err));
        }

        function openFileInfoModal(fileId, view = 'grid') {
            const modal = document.getElementById('fileModal');
            const modalContent = document.getElementById('fileModalContent');

            let url = "{{ route('media-files.show', ':id') }}".replace(':id', fileId) + "?view={{ $view }}";

            console.log(url);
            fetch(url)
                .then(res => res.text())
                .then(html => {
                    modalContent.innerHTML = html;  // data là response từ fetch API
                    modal.classList.remove('hidden');
                })
                .catch(() => {
                    modalContent.innerHTML = '<div class="text-red-500">Không thể tải nội dung.</div>';
                });
        }

        function closeFileInfoModal() {
            document.getElementById('fileModal').classList.add('hidden');
        }
    </script>

{{--  JS Button Dropdown  --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('dropdownBtn');
        const menu = document.getElementById('dropdownMenu');

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function () {
            menu.classList.add('hidden');
        });
    });
</script>
@endpush
