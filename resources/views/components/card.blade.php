<div
    draggable="true"
    ondragstart="event.dataTransfer.setData('text/plain', '{{ route('media-files.destroy', $file->id) }}')"
    class="bg-white shadow-lg rounded-2xl overflow-hidden transition hover:scale-[1.02] duration-300"
>
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
            <button type="button" onclick="openModal('{{ route('media-files.show', $file->id) }}', '{{ $viewMode }}')"
                    class="flex-1 text-center text-gray-700 border border-gray-400 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded min-w-0">
                👁 Xem
            </button>

            <a href="{{ $routeAction['edit'] }}"
               class="flex-1 text-center text-yellow-700 border border-yellow-500 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded min-w-0">
                📝 Sửa
            </a>

            <form action="{{ $routeAction['destroy'] }}" method="POST"
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
