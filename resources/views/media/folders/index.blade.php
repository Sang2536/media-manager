@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">📁 Danh sách Thư mục</h1>

            <div class="flex items-center gap-4">
                <a href="{{ route('media-folders.create') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow">
                    ➕ Thêm
                </a>

                @php $view = request()->get('view', 'grid'); @endphp
                <div class="flex gap-2">
                    <a href="{{ route('media-folders.index', ['view' => 'grid']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition
                       {{ $view === 'grid' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        🔳 Grid
                    </a>
                    <a href="{{ route('media-folders.index', ['view' => 'list']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition
                       {{ $view === 'list' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        📄 List
                    </a>
                </div>
            </div>
        </div>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="mb-6 text-green-700 bg-green-100 p-4 rounded-lg shadow">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 text-red-700 bg-red-100 p-4 rounded-lg shadow">{{ $errors->first() }}</div>
        @endif

        {{-- Breadcrumb --}}
        @if ($breadcrumbs)
            <nav class="mb-6 text-sm flex items-center flex-wrap text-gray-700">
                <a href="{{ route('media-folders.index', ['view' => $view]) }}"
                   class="text-blue-600 hover:underline flex items-center gap-1">
                    Root
                </a>

                @foreach ($breadcrumbs as $crumb)
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="{{ route('media-folders.index', ['parent' => $crumb->id, 'view' => $view]) }}"
                       class="text-blue-600 hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h4l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                        </svg>
                        {{ $crumb->name }}
                    </a>
                @endforeach
            </nav>
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
                            <button type="button" onclick="openFolderModal({{ $folder->id }}, '{{ $view }}')" class="text-green-600 hover:underline">
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
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Tên thư mục</th>
                        <th class="px-6 py-3">Folder con</th>
                        <th class="px-6 py-3">Ảnh</th>
                        <th class="px-6 py-3">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($folders as $folder)
                        <tr class="hover:bg-gray-100 transition cursor-pointer"
                            onclick="window.location='{{ route('media-folders.index', ['parent' => $folder->id, 'view' => $view]) }}'">
                            <td class="px-6 py-4 flex items-center gap-2">
                                <span>📁</span>
                                <span>{{ $folder->name }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $folder->children()->count() }}</td>
                            <td class="px-6 py-4">{{ $folder->files()->count() }}</td>
                            <td class="px-6 py-4 flex items-center gap-4"
                                onclick="event.stopPropagation();"> {{-- ngăn chặn redirect khi bấm vào "Sửa" / "Xóa" --}}
                                <button type="button" onclick="openFolderModal({{ $folder->id }}, '{{ $view }}')" class="text-green-600 hover:underline">
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
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $folders->appends(['view' => $view])->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal wrapper -->
    <div id="folderModal" class="hidden relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen">
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <button onclick="closeFolderModal()"
                        class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                <div id="folderModalContent" class="text-gray-800">
                    Đang tải...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openFolderModal(folder, view = 'grid') {
            const modal = document.getElementById('folderModal');
            const modalContent = document.getElementById('folderModalContent');

            let url = "{{ route('media-folders.show', ':id') }}".replace(':id', folder) + "?view={{ $view }}";

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

        function closeFolderModal() {
            document.getElementById('folderModal').classList.add('hidden');
        }
    </script>
@endpush
