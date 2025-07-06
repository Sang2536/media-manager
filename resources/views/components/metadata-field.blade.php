@php $metaIndex = 0; @endphp

<div>
    <label class="block font-semibold mb-1">🧾 Metadata</label>
    <div id="meta-wrapper" class="space-y-2">
        @foreach(old('metadata', $metadata) as $metadataItem)
            <div class="flex gap-2">
                <input type="text"
                       name="metadata[{{ $metaIndex }}][key]"
                       value="{{ $metadataItem['key'] ?? $metadataItem->key ?? '' }}"
                       placeholder="Key"
                       class="w-1/2 border px-2 py-1 rounded">

                <input type="text"
                       name="metadata[{{ $metaIndex }}][value]"
                       value="{{ $metadataItem['value'] ?? $metadataItem->value ?? '' }}"
                       placeholder="Value"
                       class="w-1/2 border px-2 py-1 rounded">

                <button type="button"
                        onclick="this.parentElement.remove()"
                        class="text-red-500 text-lg">&times;</button>
            </div>
            @php $metaIndex++; @endphp
        @endforeach
    </div>

    <button type="button"
            onclick="addMetaField({{ $limit }})"
            class="text-blue-600 hover:underline text-sm mt-2">
        ➕ Thêm metadata
    </button>
</div>
