@extends('layouts.app')

@section('title', 'Update Media')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">🆕 Cập nhật Media</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <strong>Đã xảy ra lỗi:</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('media-files.update', $file) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- File đã upload --}}
            <div>
                <label class="block font-semibold mb-1">📁 Chọn file <span class="text-red-500">*</span></label>

                <div class="relative border border-gray-300 rounded-lg px-4 py-3 bg-white shadow-sm">
                    <input
                        type="file"
                        name="file"
                        id="file-input"
                        accept="image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    >

                    <div class="text-gray-600 pointer-events-none">
                        {{ $file->filename ? "Đã tải: " . basename($file->filename) : "Chọn tệp từ máy tính..." }}
                    </div>
                </div>

                <div id="image-preview-container" class="mt-3 {{ $file->image_url ? '' : 'hidden' }}">
                    <img
                        id="preview-image"
                        src="{{ $file->image_url ?? '' }}"
                        alt="Image preview"
                        class="max-h-64 rounded border shadow"
                    >
                </div>
            </div>

            {{-- Tên file gốc --}}
            <div>
                <label class="block font-semibold mb-1">📝 Tên file (tùy chỉnh)</label>
                <input type="text" name="filename"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Tên gợi nhớ cho file"
                        value="{{ $file->filename }}">
            </div>

            {{-- Chọn thư mục --}}
            <div>
                {!! $renderFolderOptions !!}
            </div>

            {{-- Tag dạng Tom Select --}}
            <div>
                <label class="block font-semibold mb-1">🏷️ Tags</label>
                <select name="tags[]" id="tag-select" multiple class="w-full border rounded px-3 py-2">
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}"
                            {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Metadata key-value động bằng JS --}}
            <x-metadata-field :metadata="$file->metadata->toArray() ?? []" />

            {{-- Actions --}}
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
