@extends('layouts.app')

@section('title', 'Create Metadata')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">🆕 Thêm Metadata</h1>

        <form action="{{ route('media-metadata.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('POST')

            {{-- Select File --}}
            <div>
                <select name="media_id" id="file-select" class="w-full border px-2 py-1 rounded">
                    @foreach ($files as $item)
                        <option value="{{ $item->id }}" onclick="handleOptionSelect({{ $item->id }})">{{ $item->filename }}</option>
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
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-lg">&times;</button>
                    </div>
                </div>
                <button type="button" onclick="addMetaField()" class="text-blue-600 hover:underline text-sm mt-2">➕ Thêm metadata</button>
            </div>

            {{-- Actions--}}
            <div>
                <x-button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    name-btn="📤 Tải lên"
                />

                <x-button
                    type="reset"
                    class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow"
                    name-btn="🔄 Làm mới"
                />

                <x-button
                    :href="route('media-files.index')"
                    class="inline-flex items-center gap-2 border border-b-gray-600 bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition shadow"
                    name-btn="🔙 Quay lại"
                />
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
            if (metaIndex >= 10) {
                alert('Đã đạt giới hạn 10 metadata.');
                return false;
            }

            const wrapper = document.getElementById('meta-wrapper');
            const div = document.createElement('div');
            div.classList.add('flex', 'gap-2');

            div.innerHTML = `
                <input type="text" name="metadata[${metaIndex}][key]" placeholder="Key" class="w-1/2 border px-2 py-1 rounded">
                <input type="text" name="metadata[${metaIndex}][value]" placeholder="Value" class="w-1/2 border px-2 py-1 rounded">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-lg">&times;</button>
            `;
            wrapper.appendChild(div);
            metaIndex++;
        }
    </script>
@endpush
