@extends('layouts.app')

@section('title', 'File Manager')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <x-head-content
            title-content="📁 Danh sách Media"
            :view-mode="$view"
            :route-action="[
                    'mode' => route('media-files.index', ['view' => $view === 'grid' ? 'list' : 'grid']),
                    'create' => route('media-files.create'),
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

        {{-- Media files --}}
        @if ($view === 'grid')
            {{-- Grid view --}}
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($mediaFiles as $file)
                    <x-card
                        :file="$file"
                        :view-mode="$view"
                        :route-action="[
                            'edit' => route('media-files.edit', $file->id),
                            'destroy' => route('media-files.destroy', $file->id),
                            'show' => route('media-files.show', $file->id),
                            'copy' => '#',
                        ]"
                    />
                @empty
                    <div class="col-span-full text-center text-gray-500 py-6">Không có thư mục.</div>
                @endforelse
            </div>
        @else
            {{-- List view --}}
            <div class="bg-white shadow rounded-xl overflow-x-auto">
                <x-table
                    :view-mode="$view"
                    :headers="['Media', 'Filename', 'Created at', 'Action']"
                >
                    {{-- Table body --}}
                    @forelse ($mediaFiles as $file)
                        <tr class="hover:bg-gray-100 justify-center transition">
                            <td class="px-6 py-4 items-center gap-2">
                                <img src="{{ $file->image_url }}"
                                     alt="{{ $file->original_name }}"
                                     class="w-full h-12 object-cover" />
                            </td>
                            <td class="max-w-lg px-6 py-4">
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
                                    <button onclick="openModal('{{ route('media-files.show', $file->id) }}', '{{ $view }}')"
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
                                                class="text-center text-red-700 border border-red-500 bg-red-100 hover:bg-red-200 px-2 py-1 rounded">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có media nào.</td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        @endif

        <div class="mt-6">
            {{ $mediaFiles->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal Show Media wrapper -->
    <div id="wrapperModal" class="hidden relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen">
            <!-- Modal content -->
            <div  id="modalContent" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                Loading ...
            </div>
        </div>
    </div>
@endsection
