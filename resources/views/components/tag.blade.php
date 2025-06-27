<span id="{{ $id }}"
      class="relative inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-full
             border border-{{ $color }}-400 text-{{ $color }}-800 bg-{{ $color }}-100 hover:bg-{{ $color }}-200"
>
    <span>#{{ $tagName }}</span>

    @if($deletable && $tagId)
        <button
            class="ml-1 text-gray-500 hover:text-red-600 focus:outline-none"
            style="line-height: 1;"
            title="Xoá tag"
            onclick="handleDelete('{{ route('media-tags.destroy', $tagId) }}')"
        >
            &times;
        </button>
    @endif

    {{ $slot }}
</span>
