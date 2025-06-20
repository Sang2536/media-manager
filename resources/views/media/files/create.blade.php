@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">🆕 Thêm Media Mới</h1>

        <form action="{{ route('media-files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- File upload đẹp --}}
            <div>
                <label class="block font-semibold mb-1">📁 Chọn file <span class="text-red-500">*</span></label>
                <div class="relative border border-gray-300 rounded-lg px-4 py-3 bg-white shadow-sm">
                    <input type="file" name="file" required
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="text-gray-600 pointer-events-none">Chọn tệp từ máy tính...</div>
                </div>
            </div>

            {{-- Tên file gốc --}}
            <div>
                <label class="block font-semibold mb-1">📝 Tên file (tùy chọn)</label>
                <input type="text" name="original_name"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Tên gợi nhớ cho file">
            </div>

            {{-- Chọn thư mục --}}
            <div>
                <label class="block font-semibold mb-1">📂 Thư mục</label>
                <select name="folder_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Không có --</option>
                    @foreach ($folders as $folder)
                        <option value="{{$folder->id }}"> 📁 {{ $folder->name }}</option>
                        @foreach ($folder->children as $folderChildren)
                            <option value="{{$folderChildren->id }}"> -— {{ $folderChildren->name }} ({{ $folderChildren->path }})</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            {{-- Tag dạng Tom Select --}}
            <div>
                <label class="block font-semibold mb-1">🏷️ Tags</label>
                <select name="tags[]" id="tag-select" multiple class="w-full border rounded px-3 py-2">
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>


            {{-- Metadata key-value động bằng JS --}}
            <div>
                <label class="block font-semibold mb-1">🧾 Metadata</label>
                <div id="meta-wrapper" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="metadata[0][key]" placeholder="Key" class="w-1/2 border px-2 py-1 rounded">
                        <input type="text" name="metadata[0][value]" placeholder="Value" class="w-1/2 border px-2 py-1 rounded">
                    </div>
                </div>
                <button type="button" onclick="addMetaField()" class="text-blue-600 hover:underline text-sm mt-2">➕ Thêm metadata</button>
            </div>

            {{-- Nút Submit --}}
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    📤 Tải lên
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{--     CDN cho tags (dạng Tom Select) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    {{--    JS khởi tạo Tom Select--}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect("#tag-select", {
                create: true,
                persist: false,
                plugins: ['remove_button'],
                maxItems: null,
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                render: {
                    option_create: function(data, escape) {
                        return '<div class="create">➕ Thêm tag mới: <strong>' + escape(data.input) + '</strong></div>';
                    }
                }
            });
        });
    </script>


    {{--     JS cho metadata (thuần JavaScript) --}}
    <script>
        let metaIndex = 1;

        function addMetaField() {
            const wrapper = document.getElementById('meta-wrapper');
            const div = document.createElement('div');
            div.classList.add('flex', 'gap-2');

            div.innerHTML = `
            <input type="text" name="metadata[${metaIndex}][key]" placeholder="Key" class="w-1/2 border px-2 py-1 rounded">
            <input type="text" name="metadata[${metaIndex}][value]" placeholder="Value" class="w-1/2 border px-2 py-1 rounded">
        `;
            wrapper.appendChild(div);
            metaIndex++;
        }
    </script>
@endpush
