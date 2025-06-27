@extends('layouts.app')

@section('title', 'Metadata Manager')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <x-head-content
            title-content="📁 Danh sách Media"
            :route-action="[
                    'create' => route('media-metadata.create'),
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

        {{-- Metadata Table --}}
        <div class="bg-white shadow rounded-xl overflow-x-auto">
            <x-table
                :headers="['Id', 'File', 'Key: Value', 'Created At', 'Action']"
            >
                {{-- Table body --}}
                @forelse ($metadata as $item)
                    <tr class="hover:bg-gray-100 justify-center transition">
                        <td class="px-6 py-4">
                            <span>{{ $item->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <img src="{{ $item->file->image_url }}"
                                 alt="{{ $item->file->original_name }}"
                                 class="w-full h-36 object-cover" />
                            {{ $item->file->filename }}
                        </td>
                        <td class="px-6 py-4 w-full">
                            <div class="p-2 flex gap-2 bg-gray-600 text-white font-bold justify-center">
                                <span class="text-green-600">{{ $item->key }}</span>:
                                <span class="text-green-600">{{ $item->value }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->created_at }}</td>
                        <td class="px-6 py-4 flex items-center gap-4"
                            onclick="event.stopPropagation();"> {{-- ngăn chặn redirect khi bấm vào "Sửa" / "Xóa" --}}
                            <div class="mt-2 flex justify-between gap-2 text-sm">
                                <button onclick="openModal('{{ route('media-metadata.show', $item->id) }}')"
                                        class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                                    Xem
                                </button>

                                <a href="{{ route('media-metadata.edit', $item->id) }}"
                                   class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                                    Sửa
                                </a>

                                <form action="{{ route('media-metadata.destroy', $item->id) }}" method="POST"
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có thư mục nào.</td>
                    </tr>
                @endforelse
            </x-table>

            <div class="mt-6">
                {{ $metadata->links('pagination::tailwind') }}
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
{{--            {{ $metadata->appends()->links('pagination::tailwind') }}--}}
        </div>
    </div>
@endsection
